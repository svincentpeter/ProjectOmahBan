<?php

use Illuminate\Support\Facades\Route;

// Controller modul Sale (pakai FQCN)
use Modules\Sale\Http\Controllers\PosController;
use Modules\Sale\Http\Controllers\SaleController;
use Modules\Sale\Http\Controllers\SalePaymentsController;
use Modules\Sale\Http\Controllers\ReportController;
use Modules\Sale\Http\Controllers\CartController; // <<< [TAMBAHAN] untuk update line cart

Route::group(['middleware' => 'auth'], function () {

    // ===================== POS =====================
    Route::get('/app/pos', [PosController::class, 'index'])->name('app.pos.index');
    Route::post('/app/pos', [PosController::class, 'store'])->name('app.pos.store');

    // Endpoint AJAX: update satu baris item keranjang (qty/harga/diskon)
    // Hanya user yang punya izin edit penjualan yang boleh akses
    Route::post('/sales/cart/update-line', [CartController::class, 'updateLine'])
        ->name('sales.cart.updateLine')
        ->middleware('can:edit_sales'); // <<< [TAMBAHAN]
        // Endpoint AJAX: hapus baris & tambah item manual (edit sale)
Route::post('/sales/cart/line/remove',  [SaleController::class, 'removeLine'])
    ->name('sales.cart.removeLine')
    ->middleware('can:edit_sales');

Route::post('/sales/cart/line/add-manual', [SaleController::class, 'addManualLine'])
        ->name('sales.cart.addManual')
        ->middleware('can:edit_sales');

    // ===================== Quick View (child row) =====================
    // URL final dari server untuk menampilkan daftar item sale tertentu
    Route::get('sales/{sale}/items', [SaleController::class, 'items'])->name('sales.items');

    // ===================== PDF A4 =====================
    Route::get('/sales/pdf/{id}', function ($id) {
        $sale = \Modules\Sale\Entities\Sale::findOrFail($id);
        $pdf = \PDF::loadView('sale::print', ['sale' => $sale])->setPaper('a4');
        return $pdf->stream('sale-' . $sale->reference . '.pdf');
    })->name('sales.pdf');

    // ===================== PDF POS (A6) =====================
    Route::get('/sales/pos/pdf/{id}', function ($id) {
        $sale = \Modules\Sale\Entities\Sale::findOrFail($id);
        // Pastikan relasi user dan saleDetails sudah di-load untuk efisiensi
        $sale->load('user', 'saleDetails.product.brand');

        $pdf = \PDF::loadView('sale::print-pos', ['sale' => $sale])
            ->setPaper('a6', 'landscape')
            ->setOption('margin-top', 5)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

        return $pdf->stream('nota-' . $sale->reference . '.pdf');
    })->name('sales.pos.pdf');

    // ===================== Sales – read only =====================
    Route::resource('sales', SaleController::class)->only(['index', 'show']);
    Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])
    ->name('sales.edit')
    ->middleware('can:edit_sales');

Route::match(['put','patch'],'sales/{sale}', [SaleController::class, 'update'])
    ->name('sales.update')
    ->middleware('can:edit_sales');

    // ===================== Payments =====================
    Route::get('/sale-payments/{sale_id}', [SalePaymentsController::class, 'index'])->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', [SalePaymentsController::class, 'create'])->name('sale-payments.create');
    Route::post('/sale-payments/store', [SalePaymentsController::class, 'store'])->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', [SalePaymentsController::class, 'edit'])->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', [SalePaymentsController::class, 'update'])->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', [SalePaymentsController::class, 'destroy'])->name('sale-payments.destroy');

    // ===================== Laporan =====================
    Route::get('/sales/reports/profit', [ReportController::class, 'profitReport'])->name('sales.reports.profit');
});
