<?php

namespace App\Http\Controllers;

use App\Http\Requests\Investment\StoreInvestmentRequest;
use App\Http\Requests\Investment\UpdateInvestmentRequest;
use App\Models\Investment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    /**
     * Get all investments for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $investments = $request->user()
            ->investments()
            ->with(['transactions'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($investments);
    }

    /**
     * Store a new investment.
     */
    public function store(StoreInvestmentRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check if user has sufficient balance
        if ($user->balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient balance',
            ], 400);
        }

        $investment = $user->investments()->create($request->validated());

        // Create investment transaction
        $investment->transactions()->create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'investment',
            'description' => "Investment in {$investment->title}",
            'status' => 'completed',
        ]);

        // Update user balance
        $user->decrement('balance', $request->amount);

        return response()->json($investment->load('transactions'), 201);
    }

    /**
     * Get a specific investment.
     */
    public function show(Request $request, Investment $investment): JsonResponse
    {
        // Ensure the investment belongs to the authenticated user
        if ($investment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json($investment->load('transactions'));
    }

    /**
     * Update an investment.
     */
    public function update(UpdateInvestmentRequest $request, Investment $investment): JsonResponse
    {
        // Ensure the investment belongs to the authenticated user
        if ($investment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $investment->update($request->validated());

        return response()->json($investment->fresh());
    }

    /**
     * Delete an investment.
     */
    public function destroy(Request $request, Investment $investment): JsonResponse
    {
        // Ensure the investment belongs to the authenticated user
        if ($investment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Only allow deletion of active investments
        if ($investment->status !== 'active') {
            return response()->json([
                'message' => 'Cannot delete completed or cancelled investments',
            ], 400);
        }

        // Refund the amount to user balance
        $request->user()->increment('balance', $investment->amount);

        // Create refund transaction
        $investment->transactions()->create([
            'user_id' => $request->user()->id,
            'amount' => $investment->amount,
            'type' => 'return',
            'description' => "Refund for cancelled investment: {$investment->title}",
            'status' => 'completed',
        ]);

        $investment->update(['status' => 'cancelled']);
        $investment->delete();

        return response()->json([
            'message' => 'Investment cancelled and refunded successfully',
        ]);
    }

    /**
     * Get investment summary.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $investments = $user->investments;
        
        $totalInvested = $investments->sum('amount');
        $totalReturns = $investments->sum('actual_return');
        $activeInvestments = $investments->where('status', 'active')->count();
        $completedInvestments = $investments->where('status', 'completed')->count();

        return response()->json([
            'total_invested' => $totalInvested,
            'total_returns' => $totalReturns,
            'net_profit' => $totalReturns - $totalInvested,
            'active_investments' => $activeInvestments,
            'completed_investments' => $completedInvestments,
            'total_investments' => $investments->count(),
        ]);
    }
}
