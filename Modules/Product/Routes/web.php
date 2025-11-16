<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\CategoriesController;
use Modules\Product\Http\Controllers\ProductSecondController;
use Modules\Product\Http\Controllers\BrandController;
use Modules\Product\Http\Controllers\ServiceMasterController;

Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | PRODUCT CRUD
    |--------------------------------------------------------------------------
    */
    Route::resource('products', ProductController::class);
    Route::post('products/upload', [ProductController::class, 'uploadImage'])->name('products.dropzone.upload');

    Route::post('products/delete-image', [ProductController::class, 'deleteImage'])->name('products.dropzone.delete');
    /*
    |--------------------------------------------------------------------------
    | PRODUCT CATEGORY CRUD
    |--------------------------------------------------------------------------
    */
    Route::resource('product-categories', CategoriesController::class)->except(['create', 'show']);

    /*
    |--------------------------------------------------------------------------
    | PRODUCT SECOND (PRODUK BEKAS)
    |--------------------------------------------------------------------------
    */
    Route::resource('products-second', ProductSecondController::class)->names([
        'index' => 'products_second.index',
        'create' => 'products_second.create',
        'store' => 'products_second.store',
        'show' => 'products_second.show',
        'edit' => 'products_second.edit',
        'update' => 'products_second.update',
        'destroy' => 'products_second.destroy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | BRAND CRUD
    |--------------------------------------------------------------------------
    */
    Route::resource('brands', BrandController::class);

    /*
    |--------------------------------------------------------------------------
    | SERVICE MASTER (MARKUP JASA)
    |--------------------------------------------------------------------------
    */
    Route::prefix('service-masters')
        ->name('service-masters.')
        ->controller(ServiceMasterController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'data')->name('data');
            Route::post('/', 'store')->name('store');
            Route::put('/{serviceMaster}', 'update')->name('update');
            Route::post('/{serviceMaster}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::delete('/{serviceMaster}', 'destroy')->name('destroy');
            Route::get('/{serviceMaster}/audit-log', 'auditLog')->name('audit-log');
        });
});
