<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get all transactions for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = $request->user()
            ->transactions()
            ->with(['investment'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Store a new transaction (deposit/withdrawal).
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle deposit
        if ($data['type'] === 'deposit') {
            $user->increment('balance', $data['amount']);
            $data['status'] = 'completed';
        }
        // Handle withdrawal
        elseif ($data['type'] === 'withdrawal') {
            if ($user->balance < $data['amount']) {
                return response()->json([
                    'message' => 'Insufficient balance',
                ], 400);
            }
            $user->decrement('balance', $data['amount']);
            $data['status'] = 'completed';
        }

        $transaction = $user->transactions()->create($data);

        return response()->json($transaction, 201);
    }

    /**
     * Get a specific transaction.
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        // Ensure the transaction belongs to the authenticated user
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json($transaction->load('investment'));
    }

    /**
     * Get transaction summary.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $transactions = $user->transactions;
        
        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $totalInvestments = $transactions->where('type', 'investment')->sum('amount');
        $totalReturns = $transactions->where('type', 'return')->sum('amount');

        return response()->json([
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'total_investments' => $totalInvestments,
            'total_returns' => $totalReturns,
            'net_cash_flow' => $totalDeposits - $totalWithdrawals,
            'total_transactions' => $transactions->count(),
        ]);
    }
}
