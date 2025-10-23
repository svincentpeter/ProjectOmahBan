<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Midtrans Payment Gateway Routes
|--------------------------------------------------------------------------
|
| Route untuk menerima notification webhook dari Midtrans
| setelah payment berhasil/gagal/pending
|
*/

// Midtrans Payment Notification Webhook
Route::post('/midtrans/callback', [MidtransCallbackController::class, 'receive'])
    ->name('midtrans.callback');
