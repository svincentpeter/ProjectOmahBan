<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SaleApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Midtrans Payment Gateway Routes
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/callback', [MidtransCallbackController::class, 'receive'])
    ->name('midtrans.callback');

/*
|--------------------------------------------------------------------------
| Product & Sales API (Sanctum Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Products
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/low-stock', [ProductApiController::class, 'lowStock']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);
    Route::patch('/products/{id}/stock', [ProductApiController::class, 'updateStock']);

    // Sales
    Route::get('/sales', [SaleApiController::class, 'index']);
    Route::get('/sales/summary', [SaleApiController::class, 'summary']);
    Route::get('/sales/daily', [SaleApiController::class, 'dailySales']);
    Route::get('/sales/{id}', [SaleApiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Public API (No Auth Required)
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name')
    ]);
});
