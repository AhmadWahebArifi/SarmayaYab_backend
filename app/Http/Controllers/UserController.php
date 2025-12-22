<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user.
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        
        $user->update($request->validated());

        return response()->json([
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Get user balance.
     */
    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'balance' => $user->balance,
        ]);
    }
}
