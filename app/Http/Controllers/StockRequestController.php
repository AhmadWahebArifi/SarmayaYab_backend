<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest\StoreStockRequestRequest;
use App\Http\Requests\StockRequest\ApproveStockRequestRequest;
use App\Http\Requests\StockRequest\DispatchStockRequestRequest;
use App\Http\Requests\StockRequest\DeliverStockRequestRequest;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\WarehouseInventory;
use App\Models\BranchInventory;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = StockRequest::with(['branch', 'creator', 'reviewer', 'items.product']);

        if ($user->role === 'branch_staff' || $user->role === 'branch_manager') {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->integer('branch_id'));
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(StoreStockRequestRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'branch_staff' && $user->role !== 'branch_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $stockRequest = StockRequest::create([
                'code' => 'SR-' . now()->format('YmdHis') . '-' . rand(100, 999),
                'branch_id' => $user->branch_id,
                'created_by' => $user->id,
                'status' => 'pending',
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                StockRequestItem::create([
                    'stock_request_id' => $stockRequest->id,
                    'product_id' => $item['product_id'],
                    'requested_qty' => $item['requested_qty'],
                ]);
            }

            DB::commit();
            return response()->json($stockRequest->load('items.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(StockRequest $stockRequest): JsonResponse
    {
        return response()->json($stockRequest->load(['branch', 'creator', 'reviewer', 'items.product']));
    }

    public function approve(ApproveStockRequestRequest $request, StockRequest $stockRequest): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'warehouse_staff' && $user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($stockRequest->status !== 'pending') {
            return response()->json(['message' => 'Request is not pending'], 422);
        }

        DB::beginTransaction();
        try {
            $stockRequest->update([
                'status' => 'approved',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);

            foreach ($request->validated()['items'] as $item) {
                StockRequestItem::where('stock_request_id', $stockRequest->id)
                    ->where('product_id', $item['product_id'])
                    ->update(['approved_qty' => $item['approved_qty']]);
            }

            DB::commit();
            return response()->json($stockRequest->load('items.product'));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reject(Request $request, StockRequest $stockRequest): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'warehouse_staff' && $user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($stockRequest->status !== 'pending') {
            return response()->json(['message' => 'Request is not pending'], 422);
        }

        $stockRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Request rejected']);
    }

    public function dispatch(DispatchStockRequestRequest $request, StockRequest $stockRequest): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'warehouse_staff' && $user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($stockRequest->status !== 'approved') {
            return response()->json(['message' => 'Request is not approved'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($stockRequest->items as $item) {
                $dispatchQty = $item->approved_qty ?? 0;

                if ($dispatchQty <= 0) continue;

                $warehouseInv = WarehouseInventory::where('product_id', $item->product_id)->firstOrFail();
                if ($warehouseInv->quantity < $dispatchQty) {
                    throw new \Exception("Insufficient stock for product {$item->product->name}");
                }

                $warehouseInv->decrement('quantity', $dispatchQty);

                StockRequestItem::where('id', $item->id)->increment('dispatched_qty', $dispatchQty);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $stockRequest->branch_id,
                    'stock_request_id' => $stockRequest->id,
                    'user_id' => $user->id,
                    'type' => 'outgoing',
                    'quantity_change' => -$dispatchQty,
                    'note' => "Dispatch for request {$stockRequest->code}",
                ]);
            }

            $stockRequest->update([
                'status' => 'dispatched',
                'dispatched_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Request dispatched']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function deliver(DeliverStockRequestRequest $request, StockRequest $stockRequest): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'branch_staff' && $user->role !== 'branch_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($stockRequest->status !== 'dispatched') {
            return response()->json(['message' => 'Request is not dispatched'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($stockRequest->items as $item) {
                $receivedQty = $item->dispatched_qty;

                if ($receivedQty <= 0) continue;

                $branchInv = BranchInventory::firstOrCreate(
                    ['branch_id' => $stockRequest->branch_id, 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );

                $branchInv->increment('quantity', $receivedQty);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $stockRequest->branch_id,
                    'stock_request_id' => $stockRequest->id,
                    'user_id' => $user->id,
                    'type' => 'incoming',
                    'quantity_change' => $receivedQty,
                    'note' => "Delivery for request {$stockRequest->code}",
                ]);
            }

            $stockRequest->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Request marked as delivered']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
