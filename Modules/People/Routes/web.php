<?php

use Illuminate\Support\Facades\Route;
use Modules\People\Http\Controllers\SuppliersController;
use Modules\People\Http\Controllers\CustomersController;

Route::group(['middleware' => 'auth'], function () {
    // ========================================
    // SUPPLIERS ROUTES
    // ========================================
    Route::resource('suppliers', SuppliersController::class)->names([
        'index' => 'suppliers.index',
        'create' => 'suppliers.create',
        'store' => 'suppliers.store',
        'show' => 'suppliers.show',
        'edit' => 'suppliers.edit',
        'update' => 'suppliers.update',
        'destroy' => 'suppliers.destroy',
    ]);

    Route::prefix('suppliers')
        ->name('suppliers.')
        ->group(function () {
            Route::get('/list/dropdown', [SuppliersController::class, 'getSuppliers'])->name('list');
            Route::get('/archived/all', [SuppliersController::class, 'archived'])->name('archived');
            Route::post('/{id}/restore', [SuppliersController::class, 'restore'])->name('restore');
            Route::get('/statistics/data', [SuppliersController::class, 'statistics'])->name('statistics');
            Route::get('/export/excel', [SuppliersController::class, 'export'])->name('export');
        });

    // ========================================
    // CUSTOMERS ROUTES
    // ========================================
    Route::resource('customers', CustomersController::class)->names([
        'index' => 'customers.index',
        'create' => 'customers.create',
        'store' => 'customers.store',
        'show' => 'customers.show',
        'edit' => 'customers.edit',
        'update' => 'customers.update',
        'destroy' => 'customers.destroy',
    ]);

    Route::prefix('customers')
        ->name('customers.')
        ->group(function () {
            // ⭐ API untuk Select2 dropdown
            Route::get('/list/dropdown', [CustomersController::class, 'getCustomers'])->name('list');

            // ⭐ Quick add customer dari POS
            Route::post('/store-or-get', [CustomersController::class, 'storeOrGet'])->name('store-or-get');

            // Extra routes lainnya
            Route::get('/archived/all', [CustomersController::class, 'archived'])->name('archived');
            Route::post('/{id}/restore', [CustomersController::class, 'restore'])->name('restore');
            Route::get('/statistics/data', [CustomersController::class, 'statistics'])->name('statistics');
        });
});
