<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\MidtransCallbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/sales-purchases/chart-data', [HomeController::class, 'salesPurchasesChart'])->name('sales-purchases.chart');
    Route::get('/current-month/chart-data', [HomeController::class, 'currentMonthChart'])->name('current-month.chart');
    Route::get('/payment-flow/chart-data', [HomeController::class, 'paymentChart'])->name('payment-flow.chart');
});

// Route untuk callback Midtrans (HARUS di-exclude dari CSRF)
Route::post('/api/midtrans/callback', [MidtransCallbackController::class, 'receive'])->name('midtrans.callback');
