<?php

use Illuminate\Support\Facades\Route;

// Controller modul Sale (pakai FQCN)
use Modules\Sale\Http\Controllers\PosController;
use Modules\Sale\Http\Controllers\SaleController;
use Modules\Sale\Http\Controllers\SalePaymentsController;
use Modules\Sale\Http\Controllers\ReportController;
use Modules\Sale\Http\Controllers\CartController; // <<< [TAMBAHAN] untuk update line cart

Route::middleware('auth')->group(function () {
    // ===================== POS =====================
    Route::get('/app/pos', [PosController::class, 'index'])->name('app.pos.index');
    Route::post('/app/pos', [PosController::class, 'store'])->name('app.pos.store'); // (opsional) jalur klasik

    // ========== Cart (Edit Sale) ==========
    Route::post('/sales/cart/update-line', [CartController::class, 'updateLine'])
        ->name('sales.cart.updateLine')
        ->middleware('can:edit_sales');

    Route::post('/sales/cart/line/remove', [SaleController::class, 'removeLine'])
        ->name('sales.cart.removeLine')
        ->middleware('can:edit_sales');

    Route::post('/sales/cart/line/add-manual', [CartController::class, 'addManualLine']) // << pindah ke CartController
        ->name('sales.cart.addManual')
        ->middleware('can:edit_sales');

    // ===================== Quick View (child row) =====================
    Route::get('sales/{sale}/items', [SaleController::class, 'items'])->name('sales.items');

    // ===================== PDF A4 =====================
    // GANTI closure -> controller method (lebih rapih & testable)
    Route::get('/sales/pdf/{sale}', [SaleController::class, 'printA4'])
        ->name('sales.pdf')
        ->middleware('can:access_sales');

    // ===================== PDF POS (A6) =====================
    // GANTI closure -> controller method; dipakai untuk INVOICE (Draft) & STRUK (Paid)
    Route::get('/sales/pos/pdf/{sale}', [PosController::class, 'printPos'])
        ->name('sales.pos.pdf')
        ->middleware('can:access_sales');

    // ===================== Sales – read only =====================
    Route::resource('sales', SaleController::class)->only(['index', 'show']);

    Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])
        ->name('sales.edit')
        ->middleware('can:edit_sales');

    Route::match(['put', 'patch'], 'sales/{sale}', [SaleController::class, 'update'])
        ->name('sales.update')
        ->middleware('can:edit_sales');

    // ===================== Payments (AJAX - tanpa role dulu) =====================
    Route::prefix('/sale-payments')->group(function () {
        Route::post('/ajax/store', [SalePaymentsController::class, 'ajaxStore'])->name('sale-payments.ajax.store');
        Route::patch('/ajax/{salePayment}', [SalePaymentsController::class, 'ajaxUpdate'])->name('sale-payments.ajax.update');
        Route::delete('/ajax/{salePayment}', [SalePaymentsController::class, 'ajaxDestroy'])->name('sale-payments.ajax.destroy');
        Route::get('/ajax/summary/{sale}', [SalePaymentsController::class, 'ajaxSummary'])->name('sale-payments.ajax.summary');
    });

    // ===================== Payments (Halaman klasik - tanpa role dulu) =====================
    Route::get('/sale-payments/{sale_id}', [SalePaymentsController::class, 'index'])->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', [SalePaymentsController::class, 'create'])->name('sale-payments.create');
    Route::post('/sale-payments/store', [SalePaymentsController::class, 'store'])->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', [SalePaymentsController::class, 'edit'])->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', [SalePaymentsController::class, 'update'])->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', [SalePaymentsController::class, 'destroy'])->name('sale-payments.destroy');

    Route::prefix('sales/{sale}/payments')
        ->name('sale-payments.')
        ->group(function () {
            Route::get('/', [\Modules\Sale\Http\Controllers\SalePaymentsController::class, 'index'])->name('index');
            Route::get('/data', [\Modules\Sale\Http\Controllers\SalePaymentsController::class, 'datatable'])->name('datatable');
            Route::delete('/{payment}', [\Modules\Sale\Http\Controllers\SalePaymentsController::class, 'destroy'])->name('destroy');
        });

    // Laporan stok menipis
    Route::get('/sales/reports/low-stock', [ReportController::class, 'lowStockReport'])
        ->name('sales.reports.low_stock')
        ->middleware('can:access_reports');

    // ===================== Laporan =====================
    Route::get('/sales/reports/profit', [ReportController::class, 'profitReport'])->name('sales.reports.profit');
});

// ======================================================================
// TAHAP 1: VARIANCE MONITORING - Routes (Sale Module)
// ======================================================================

// Variance Monitoring (Owner - Monitor Deviasi Harga)
Route::middleware(['auth'])
    ->prefix('sale')
    ->name('sale.')
    ->group(function () {
        // Dashboard deviasi
        Route::get('variance-monitoring', 'VarianceMonitoringController@index')->name('variance-monitoring.index');

        // API DataTable
        Route::get('variance-monitoring/data', 'VarianceMonitoringController@dataTable')->name('variance-monitoring.data');

        // Export
        Route::get('variance-monitoring/export', 'VarianceMonitoringController@export')->name('variance-monitoring.export');

        // Detail satu deviasi
        Route::get('variance-monitoring/{id}', 'VarianceMonitoringController@show')->name('variance-monitoring.show');

        // Approve deviasi
        Route::post('variance-monitoring/{id}/approve', 'VarianceMonitoringController@approve')->name('variance-monitoring.approve');

        // Reject deviasi
        Route::post('variance-monitoring/{id}/reject', 'VarianceMonitoringController@reject')->name('variance-monitoring.reject');
    });
