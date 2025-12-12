<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $pdf = Pdf::loadView('purchase::second.print', [
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

        $pdf = Pdf::loadView('purchase::baru.print', [
            'purchase' => $purchase,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-' . $purchase->reference . '.pdf');
    })->name('purchases.pdf');

    // Resource Routes untuk Produk Baru
    Route::resource('purchases', 'PurchaseController');

    // Purchase Payments
    Route::get('purchase-payments/{purchase_id}', 'PurchasePaymentsController@index')->name('purchase-payments.index');
    Route::get('purchase-payments/{purchase_id}/create', 'PurchasePaymentsController@create')->name('purchase-payments.create');
    Route::post('purchase-payments', 'PurchasePaymentsController@store')->name('purchase-payments.store');
    Route::get('purchase-payments/{purchase_id}/{purchasePayment}/edit', 'PurchasePaymentsController@edit')->name('purchase-payments.edit');
    Route::patch('purchase-payments/{purchasePayment}', 'PurchasePaymentsController@update')->name('purchase-payments.update');
    Route::delete('purchase-payments/{purchasePayment}', 'PurchasePaymentsController@destroy')->name('purchase-payments.destroy');
});
