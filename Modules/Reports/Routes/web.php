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
        // Index (Redirect to Daily)
        Route::get('/', [ReportsController::class, 'index'])->name('index');

        // Laporan Kas Harian
        Route::get('/daily', function () {
            return view('livewire.reports.livewire-host', [
                'title' => 'Laporan Kas Harian',
                'component' => 'reports.daily-report',
            ]);
        })->name('daily.index');

        // Laba Rugi
        Route::get('/profit-loss', function () {
            return view('livewire.reports.livewire-host', [
                'title' => 'Laporan Laba / Rugi',
                'component' => 'reports.profit-loss-report',
            ]);
        })->name('profit_loss.index');

    });

// ======================================================
// Alias/redirect untuk NAMA LAMA (agar menu fallback tetap jalan)
// ======================================================
Route::middleware(['web', 'auth', 'can:access_reports'])->group(function () {
    // Ringkas per kasir (route lama yang diinginkan sidebar)
    Route::get('/reports/ringkas/cashier', [ReportsController::class, 'ringkasCashier'])->name('ringkas-report.cashier');
});
