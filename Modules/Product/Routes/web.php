<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {
    // Print Barcode
    Route::get('/products/print-barcode', 'BarcodeController@printBarcode')->name('barcode.print');
    
    // Product
    Route::resource('products', 'ProductController');
    Route::post('/products/upload', 'ProductController@uploadImage')->name('dropzone.upload');
    
    // Product Category
    Route::resource('product-categories', 'CategoriesController')->except('create', 'show');

    // ====================================================================
    // BAGIAN YANG DIPERBAIKI ADA DI BAWAH INI
    // ====================================================================

    // Rute CRUD untuk Produk Bekas
    Route::resource('products-second', 'ProductSecondController')->names([
        'index'   => 'products_second.index',
        'create'  => 'products_second.create',
        'store'   => 'products_second.store',
        'show'    => 'products_second.show',
        'edit'    => 'products_second.edit',
        'update'  => 'products_second.update',
        'destroy' => 'products_second.destroy',
    ]);
    
    // Rute CRUD untuk Merek (dipisahkan menjadi pendaftaran sendiri)
    Route::resource('brands', 'BrandController');

});