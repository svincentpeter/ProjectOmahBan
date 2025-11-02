<?php

use Illuminate\Support\Facades\Route;
use Modules\Adjustment\Http\Controllers\AdjustmentController;

Route::middleware(['auth'])->group(function () {

    // --- Cetak PDF (HARUS sebelum resource agar tidak bentrok dengan show) ---
    Route::get('adjustments/{adjustment}/pdf', [AdjustmentController::class, 'pdf'])
        ->name('adjustments.pdf');

    // --- Halaman Approval (daftar yang pending) ---
    Route::get('adjustments-approval', [AdjustmentController::class, 'approvals'])
        ->name('adjustments.approvals')
        ->middleware('permission:approve_adjustments');

    // --- DataTables source untuk halaman Approval (INI YANG DIBUTUHKAN) ---
    //   Nama route: adjustments.getPendingAdjustments
    Route::get('adjustments-approval/data', [AdjustmentController::class, 'pendingDatatable'])
        ->name('adjustments.getPendingAdjustments')
        ->middleware('permission:approve_adjustments');

    // --- Aksi Approve / Reject ---
    Route::post('adjustments/{adjustment}/approve', [AdjustmentController::class, 'approve'])
        ->name('adjustments.approve')
        ->middleware('permission:approve_adjustments');

    // --- Export (opsional) ---
    Route::get('adjustments/export', [AdjustmentController::class, 'export'])
        ->name('adjustments.export')
        ->middleware('permission:access_adjustments');

    // --- CRUD utama ---
    Route::resource('adjustments', AdjustmentController::class);
    Route::get('adjustments/datatable/data', [AdjustmentController::class, 'getDataTable'])
    ->name('adjustments.datatable.data');

});
