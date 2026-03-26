<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\InventoryAnalyticsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/user/balance', [UserController::class, 'balance']);

    Route::get('/investments', [InvestmentController::class, 'index']);
    Route::post('/investments', [InvestmentController::class, 'store']);
    Route::get('/investments/{investment}', [InvestmentController::class, 'show']);
    Route::put('/investments/{investment}', [InvestmentController::class, 'update']);
    Route::delete('/investments/{investment}', [InvestmentController::class, 'destroy']);
    Route::get('/investments/summary', [InvestmentController::class, 'summary']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::get('/transactions/summary', [TransactionController::class, 'summary']);

    // Inventory system routes
    Route::middleware('role:admin,warehouse_staff')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::apiResource('branches', BranchController::class);
    });

    Route::middleware('role:admin,warehouse_staff,branch_manager,branch_staff')->group(function () {
        Route::get('/stock-requests', [StockRequestController::class, 'index']);
        Route::post('/stock-requests', [StockRequestController::class, 'store']);
        Route::get('/stock-requests/{stockRequest}', [StockRequestController::class, 'show']);
    });

    Route::middleware('role:admin,warehouse_staff')->group(function () {
        Route::post('/stock-requests/{stockRequest}/approve', [StockRequestController::class, 'approve']);
        Route::post('/stock-requests/{stockRequest}/reject', [StockRequestController::class, 'reject']);
        Route::post('/stock-requests/{stockRequest}/dispatch', [StockRequestController::class, 'dispatch']);
    });

    Route::middleware('role:admin,branch_manager,branch_staff')->group(function () {
        Route::post('/stock-requests/{stockRequest}/deliver', [StockRequestController::class, 'deliver']);
    });

    // Analytics Routes
    Route::middleware('role:admin,warehouse_staff,branch_manager,branch_staff')->group(function () {
        Route::get('/analytics/dashboard', [InventoryAnalyticsController::class, 'dashboard']);
        Route::get('/analytics/product-suggestions', [InventoryAnalyticsController::class, 'productSuggestions']);
        Route::get('/analytics/auto-reorder-suggestions', [InventoryAnalyticsController::class, 'autoReorderSuggestions']);
    });
});
