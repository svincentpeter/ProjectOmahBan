<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportsController;

// ======================================================
// Reports (nama rute diselaraskan dgn Menu.blade)
// ======================================================
Route::middleware(['web', 'auth', 'can:access_reports'])
    ->prefix('reports')
    ->name('reports.')
    ->group(function () {

        // Laporan Kas Harian
        Route::get('/daily', [ReportsController::class, 'dailyIndex'])
            ->name('daily.index');
        Route::post('/daily', [ReportsController::class, 'generateDailyReport'])
            ->name('daily.generate');

        // Laba Rugi
        Route::get('/profit-loss', [ReportsController::class, 'profitLossIndex'])
            ->name('profit_loss.index');
        Route::post('/profit-loss', [ReportsController::class, 'generateProfitLossReport'])
            ->name('profit_loss.generate');

        // Pembayaran
        Route::get('/payments', [ReportsController::class, 'paymentsIndex'])
            ->name('payments.index');
        Route::post('/payments', [ReportsController::class, 'generatePaymentsReport'])
            ->name('payments.generate');

        // Penjualan
        Route::get('/sales', [ReportsController::class, 'salesIndex'])
            ->name('sales.index');
        Route::post('/sales', [ReportsController::class, 'generateSalesReport'])
            ->name('sales.generate');

        // Pembelian
        Route::get('/purchases', [ReportsController::class, 'purchasesIndex'])
            ->name('purchases.index');
        Route::post('/purchases', [ReportsController::class, 'generatePurchasesReport'])
            ->name('purchases.generate');

        // Retur Penjualan
        Route::get('/sales-return', [ReportsController::class, 'salesReturnIndex'])
            ->name('sales_return.index');
        Route::post('/sales-return', [ReportsController::class, 'generateSalesReturnReport'])
            ->name('sales_return.generate');

        // Retur Pembelian
        Route::get('/purchases-return', [ReportsController::class, 'purchasesReturnIndex'])
            ->name('purchases_return.index');
        Route::post('/purchases-return', [ReportsController::class, 'generatePurchasesReturnReport'])
            ->name('purchases_return.generate');
    });

// ======================================================
// Alias/redirect untuk NAMA LAMA (agar menu fallback tetap jalan)
// ======================================================
Route::middleware(['web','auth','can:access_reports'])->group(function () {

    // Daily cash report
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportsController::class, 'dailyIndex'])->name('daily.index');
        Route::post('/daily', [ReportsController::class, 'generateDailyReport'])->name('daily.generate');

        // Profit & Loss
        Route::get('/profit-loss', [ReportsController::class, 'profitLossIndex'])->name('profit_loss.index');
        Route::post('/profit-loss', [ReportsController::class, 'generateProfitLossReport'])->name('profit_loss.generate');
    });

    // Ringkas per kasir (route lama yang diinginkan sidebar)
    Route::get('/reports/ringkas/cashier', [ReportsController::class, 'ringkasCashier'])
        ->name('ringkas-report.cashier');
});
