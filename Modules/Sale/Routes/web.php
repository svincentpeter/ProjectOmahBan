<?php

use Illuminate\Support\Facades\Route;

// Controller modul Sale (pakai FQCN)
use Modules\Sale\Http\Controllers\PosController;
use Modules\Sale\Http\Controllers\SaleController;
use Modules\Sale\Http\Controllers\SalePaymentsController;
use Modules\Sale\Http\Controllers\ReportController;
use Modules\Sale\Http\Controllers\CartController;
use Modules\Sale\Http\Controllers\VarianceMonitoringController;

// ============================
// Group Middleware Auth
// ============================
Route::middleware('auth')->group(function () {
    // ===================== POS =====================
    Route::get('/app/pos', [PosController::class, 'index'])->name('app.pos.index');
    Route::post('/app/pos', [PosController::class, 'store'])->name('app.pos.store');

    // ===================== POS Optimized Actions =====================
    Route::post('/pos/cart/add', [PosController::class, 'addToCart'])->name('pos.cart.add');

    Route::post('/sales/cart/update-line', [CartController::class, 'updateLine'])
        ->name('sales.cart.updateLine')
        ->middleware('can:edit_sales');
    Route::post('/sales/cart/line/remove', [SaleController::class, 'removeLine'])
        ->name('sales.cart.removeLine')
        ->middleware('can:edit_sales');
    Route::post('/sales/cart/line/add-manual', [CartController::class, 'addManualLine'])
        ->name('sales.cart.addManual')
        ->middleware('can:edit_sales');

    // ===================== PDF Routes (HARUS DI ATAS {sale}) =====================
    Route::get('/sales/pdf/{sale}', [SaleController::class, 'printA4'])
        ->name('sales.pdf')
        ->middleware('can:access_sales');
    Route::get('/sales/pos/pdf/{sale}', [PosController::class, 'printPos'])
        ->name('sales.pos.pdf')
        ->middleware('can:access_sales');

    // ===================== Sales Edit & Update =====================
    Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])
        ->name('sales.edit')
        ->middleware('can:edit_sales');
    Route::match(['put', 'patch'], 'sales/{sale}', [SaleController::class, 'update'])
        ->name('sales.update')
        ->middleware('can:edit_sales');
    Route::delete('sales/{sale}', [SaleController::class, 'destroy'])
        ->name('sales.destroy')
        ->middleware('can:delete_sales');

    // ===================== Sales (Protected by can:access_sales) =====================
    Route::prefix('sales')
        ->name('sales.')
        ->middleware('can:access_sales')
        ->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('/create', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
            Route::get('/summary', [SaleController::class, 'summary'])->name('summary');
            Route::get('/{sale}/items', [SaleController::class, 'items'])->name('items');
            Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        });

    // ===================== Payments (AJAX) =====================
    Route::prefix('/sale-payments/ajax')
        ->name('sale-payments.ajax.')
        ->group(function () {
            Route::post('/store', [SalePaymentsController::class, 'ajaxStore'])->name('store');
            Route::patch('/{salePayment}', [SalePaymentsController::class, 'ajaxUpdate'])->name('update');
            Route::delete('/{salePayment}', [SalePaymentsController::class, 'ajaxDestroy'])->name('destroy');
            Route::get('/summary/{sale}', [SalePaymentsController::class, 'ajaxSummary'])->name('summary');
        });

    // ===================== Payments (Halaman Klasik) =====================
    Route::get('/sale-payments/{sale_id}', [SalePaymentsController::class, 'index'])->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', [SalePaymentsController::class, 'create'])->name('sale-payments.create');
    Route::post('/sale-payments/store', [SalePaymentsController::class, 'store'])->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', [SalePaymentsController::class, 'edit'])->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', [SalePaymentsController::class, 'update'])->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', [SalePaymentsController::class, 'destroy'])->name('sale-payments.destroy');

    // ===================== Laporan =====================
    Route::get('/sales/reports/profit', [ReportController::class, 'profitReport'])->name('sales.reports.profit');
    Route::get('/sales/reports/low-stock', [ReportController::class, 'lowStockReport'])
        ->name('sales.reports.low_stock')
        ->middleware('can:access_reports');
});

// ============================
// VARIANCE MONITORING
// ============================
Route::middleware('auth')
    ->prefix('sale')
    ->name('sale.')
    ->group(function () {
        Route::prefix('variance-monitoring')
            ->name('variance-monitoring.')
            ->controller(VarianceMonitoringController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/data', 'dataTable')->name('data');
                Route::get('/export', 'export')->name('export');
                Route::get('/{id}', 'show')->name('show');
                Route::post('/{id}/approve', 'approve')->name('approve');
                Route::post('/{id}/reject', 'reject')->name('reject');
            });
    });

    