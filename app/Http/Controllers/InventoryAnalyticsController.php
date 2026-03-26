<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\Product;
use App\Models\WarehouseInventory;
use App\Models\BranchInventory;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAnalyticsController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $user->role === 'admin' || $user->role === 'warehouse_staff' 
            ? null 
            : $user->branch_id;

        return response()->json([
            'summary' => $this->getSummaryStats($branchId),
            'recentRequests' => $this->getRecentRequests($branchId),
            'lowStockAlerts' => $this->getLowStockAlerts($branchId),
            'topProducts' => $this->getTopProducts($branchId),
            'requestTrends' => $this->getRequestTrends($branchId),
        ]);
    }

    private function getSummaryStats(?int $branchId): array
    {
        $query = StockRequest::query();
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return [
            'total_requests' => $query->count(),
            'pending_requests' => $query->where('status', 'pending')->count(),
            'approved_requests' => $query->where('status', 'approved')->count(),
            'dispatched_requests' => $query->where('status', 'dispatched')->count(),
            'delivered_requests' => $query->where('status', 'delivered')->count(),
            'urgent_requests' => $query->where('priority', 'urgent')->count(),
            'total_value' => $query->sum('total_value'),
        ];
    }

    private function getRecentRequests(?int $branchId): array
    {
        $query = StockRequest::with(['branch', 'creator', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get()->toArray();
    }

    private function getLowStockAlerts(?int $branchId): array
    {
        if ($branchId) {
            return BranchInventory::with('product')
                ->where('branch_id', $branchId)
                ->whereHas('product', function ($q) {
                    $q->whereRaw('branch_inventories.quantity <= products.reorder_point');
                })
                ->get()
                ->toArray();
        }

        return WarehouseInventory::with('product')
            ->whereRaw('warehouse_inventories.quantity <= products.reorder_point')
            ->get()
            ->toArray();
    }

    private function getTopProducts(?int $branchId): array
    {
        $query = StockMovement::select('products.name', 'products.sku')
            ->selectRaw('COUNT(*) as request_count, SUM(ABS(quantity_change)) as total_quantity')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->where('stock_movements.created_at', '>=', now()->subDays(30))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('request_count', 'desc')
            ->limit(10);

        if ($branchId) {
            $query->where('stock_movements.branch_id', $branchId);
        }

        return $query->get()->toArray();
    }

    private function getRequestTrends(?int $branchId): array
    {
        $query = StockRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get()->toArray();
    }

    public function productSuggestions(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $user->branch_id;

        // Get frequently requested products for this branch
        $frequentProducts = StockRequest::select('products.*', DB::raw('COUNT(*) as request_count'))
            ->join('stock_request_items', 'stock_requests.id', '=', 'stock_request_items.stock_request_id')
            ->join('products', 'stock_request_items.product_id', '=', 'products.id')
            ->where('stock_requests.branch_id', $branchId)
            ->where('stock_requests.created_at', '>=', now()->subDays(60))
            ->groupBy('products.id')
            ->orderBy('request_count', 'desc')
            ->limit(5)
            ->get();

        // Get products with low stock at branch
        $lowStockProducts = Product::with(['branchInventory' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->whereHas('branchInventory', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereRaw('branch_inventories.quantity <= products.reorder_point');
            })
            ->get();

        return response()->json([
            'frequently_requested' => $frequentProducts,
            'low_stock' => $lowStockProducts,
        ]);
    }

    public function autoReorderSuggestions(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $user->branch_id;

        $suggestions = Product::with(['branchInventory' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->whereHas('branchInventory', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->whereRaw('branch_inventories.quantity <= products.reorder_point');
            })
            ->get()
            ->map(function ($product) {
                $currentStock = $product->branchInventory->first()?->quantity ?? 0;
                $suggestedQty = max($product->reorder_point * 2 - $currentStock, $product->reorder_point);
                
                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $currentStock,
                    'reorder_point' => $product->reorder_point,
                    'suggested_quantity' => $suggestedQty,
                    'estimated_value' => $suggestedQty * $product->selling_price,
                ];
            });

        return response()->json($suggestions);
    }
}
