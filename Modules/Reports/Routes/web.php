<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportsController;
use App\Livewire\Reports\DailyReport as LwDailyReport;
use App\Livewire\Reports\ProfitLossReport as LwProfitLossReport;


// ======================================================
// Reports (nama rute diselaraskan dgn Menu.blade)
// ======================================================
Route::middleware(['web', 'auth', 'can:access_reports'])
    ->prefix('reports')
    ->name('reports.')
    ->group(function () {

        // Laporan Kas Harian
        Route::get('/daily', function () {
            return view('livewire.reports.livewire-host', [
    'title'     => 'Laporan Kas Harian',
    'component' => 'reports.daily-report',
]);

        })->name('daily.index');

        Route::post('/daily', [ReportsController::class, 'generateDailyReport'])
            ->name('daily.generate');

        // Laba Rugi
        Route::get('/profit-loss', function () {
            return view('livewire.reports.livewire-host', [
                'title'     => 'Laporan Laba / Rugi',
                'component' => 'reports.profit-loss-report',
            ]);
        })->name('profit_loss.index');

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
Route::middleware(['web', 'auth', 'can:access_reports'])->group(function () {

    // Ringkas per kasir (route lama yang diinginkan sidebar)
    Route::get('/reports/ringkas/cashier', [ReportsController::class, 'ringkasCashier'])
        ->name('ringkas-report.cashier');
});
