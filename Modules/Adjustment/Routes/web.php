<?php

use Illuminate\Support\Facades\Route;
use Modules\Adjustment\Http\Controllers\AdjustmentController;

Route::middleware('auth')->group(function () {
    // ---- PDF: taruh di atas resource agar tidak tertangkap oleh 'show'
    Route::get('adjustments/{adjustment}/pdf', [AdjustmentController::class, 'pdf'])->name('adjustments.pdf');

    // ---- Approval System
    // Daftar pending approvals
    Route::get('adjustments-approval', [AdjustmentController::class, 'approvals'])
        ->name('adjustments.approvals')
        ->middleware('permission:approve_adjustments');

    // Action approve/reject (POST)
    Route::post('adjustments/{adjustment}/approve', [AdjustmentController::class, 'approve'])
        ->name('adjustments.approve')
        ->middleware('permission:approve_adjustments');

    // ---- CRUD adjustments (existing)
    Route::resource('adjustments', AdjustmentController::class);

    Route::get('adjustments-approval/data', [AdjustmentController::class, 'getPendingAdjustments'])
        ->name('adjustments.getPendingAdjustments')
        ->middleware('permission:approve_adjustments');
});
