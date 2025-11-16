<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Purchase Module
|--------------------------------------------------------------------------
|
| Routes untuk Purchase (Produk Baru dari Supplier) dan
| PurchaseSecond (Produk Bekas dari Customer)
|
| ⚠️ PENTING:
| - Semua route /purchases/second/* HARUS didefinisikan
|   SEBELUM Route::resource('purchases', ...)
*/

Route::group(['middleware' => 'auth'], function () {
    // ======================================================================
    // PURCHASE PRODUK BEKAS (dari Customer)
    // ======================================================================

    // 1. PDF Route (paling spesifik)
    Route::get('/purchases/second/pdf/{id}', function ($id) {
        $purchaseSecond = \Modules\Purchase\Entities\PurchaseSecond::findOrFail($id);

        $pdf = \PDF::loadView('purchase::second.print', [
            'purchase' => $purchaseSecond,
        ])->setPaper('a4');

        return $pdf->stream('purchase-second-' . $purchaseSecond->reference . '.pdf');
    })->name('purchases.second.pdf');

    // 2. Index Route
    Route::get('purchases/second', 'PurchaseSecondController@index')->name('purchases.second.index');

    // 3. Create Route
    Route::get('purchases/second/create', 'PurchaseSecondController@create')->name('purchases.second.create');

    // 4. Store Route
    Route::post('purchases/second', 'PurchaseSecondController@store')->name('purchases.second.store');

    // 5. Edit Route
    Route::get('purchases/second/{purchaseSecond}/edit', 'PurchaseSecondController@edit')->name('purchases.second.edit');

    // 6. Update Route
    Route::patch('purchases/second/{purchaseSecond}', 'PurchaseSecondController@update')->name('purchases.second.update');

    // 7. Destroy Route
    Route::delete('purchases/second/{purchaseSecond}', 'PurchaseSecondController@destroy')->name('purchases.second.destroy');

    // 8. Show Route (paling generic - HARUS TERAKHIR DI BLOK SECOND)
    Route::get('purchases/second/{purchaseSecond}', 'PurchaseSecondController@show')->name('purchases.second.show');

    // ======================================================================
    // PURCHASE PRODUK BARU (dari Supplier)
    // ======================================================================

    // ⚠️ PDF Route HARUS di atas resource (lebih spesifik)
    Route::get('/purchases/pdf/{id}', function ($id) {
        $purchase = \Modules\Purchase\Entities\Purchase::findOrFail($id);
        $supplier = \Modules\People\Entities\Supplier::findOrFail($purchase->supplier_id);

        $pdf = \PDF::loadView('purchase::print', [
            'purchase' => $purchase,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-' . $purchase->reference . '.pdf');
    })->name('purchases.pdf');

    // Resource Routes untuk Produk Baru
    Route::resource('purchases', 'PurchaseController');
});

// ===== TEST ROUTE - DELETE AFTER SUCCESS =====
Route::get('purchases/second/alive', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Route Module Purchase WORKS! ✅',
        'controller_exists' => class_exists('Modules\Purchase\Http\Controllers\PurchaseSecondController'),
        'namespace' => 'Modules\Purchase\Http\Controllers',
    ]);
});
