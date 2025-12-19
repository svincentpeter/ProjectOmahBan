<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Owner\NotificationController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ManualInputController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('auth.login'))->middleware('guest');

Auth::routes(['register' => false]);

// ======================================================================
// DASHBOARD (auth)
// ======================================================================
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/sales-purchases/chart-data', [HomeController::class, 'salesPurchasesChart'])->name('sales-purchases.chart');
    Route::get('/current-month/chart-data', [HomeController::class, 'currentMonthChart'])->name('current-month.chart');
    Route::get('/payment-flow/chart-data', [HomeController::class, 'paymentChart'])->name('payment-flow.chart');
    
    // Audit Log
    Route::get('/audit-log', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-log.index');

    // WhatsApp Settings
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\WhatsAppController::class, 'settings'])->name('settings');
        Route::get('/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('status');
        Route::get('/qr', [\App\Http\Controllers\WhatsAppController::class, 'qrCode'])->name('qr');
        Route::post('/test', [\App\Http\Controllers\WhatsAppController::class, 'testMessage'])->name('test');
        Route::post('/notify-owner', [\App\Http\Controllers\WhatsAppController::class, 'notifyOwner'])->name('notify-owner');
        Route::post('/reconnect', [\App\Http\Controllers\WhatsAppController::class, 'reconnect'])->name('reconnect');
        Route::post('/disconnect', [\App\Http\Controllers\WhatsAppController::class, 'disconnect'])->name('disconnect');
        
        // Notification Settings
        Route::post('/notifications/{setting}/toggle', [\App\Http\Controllers\WhatsAppController::class, 'toggleNotification'])->name('notifications.toggle');
        Route::post('/notifications/{setting}/template', [\App\Http\Controllers\WhatsAppController::class, 'updateTemplate'])->name('notifications.update-template');
        Route::post('/notifications/{setting}/reset', [\App\Http\Controllers\WhatsAppController::class, 'resetTemplate'])->name('notifications.reset-template');

        // Recipient Management
        Route::post('/recipients', [\App\Http\Controllers\WhatsAppController::class, 'storeRecipient'])->name('recipients.store');
        Route::put('/recipients/{recipient}', [\App\Http\Controllers\WhatsAppController::class, 'updateRecipient'])->name('recipients.update');
        Route::delete('/recipients/{recipient}', [\App\Http\Controllers\WhatsAppController::class, 'deleteRecipient'])->name('recipients.delete');
    });
});

// ======================================================================
// WEBHOOKS (PUBLIC, CSRF-EXEMPT) → tempatkan DI LUAR middleware auth
// ======================================================================
Route::post('/api/midtrans/callback', [MidtransCallbackController::class, 'receive'])->name('midtrans.callback');

// ======================================================================
// OWNER NOTIFICATION CENTER (auth)
// ======================================================================
Route::prefix('notifications')
    ->name('notifications.')
    ->middleware('auth')
    ->group(function () {
        // ===== API/AJAX (letakkan di atas) =====
        Route::match(['get', 'post'], 'data', [NotificationController::class, 'data'])->name('data');
        Route::get('unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('latest', [NotificationController::class, 'getLatest'])->name('latest');
        Route::get('summary', [NotificationController::class, 'summary'])->name('summary');
        Route::get('export', [NotificationController::class, 'export'])->name('export');

        Route::post('destroy-bulk', [NotificationController::class, 'destroyBulk'])->name('destroy-bulk');

        // Mark all read (versi form & versi API)
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('mark-all-read-api', [NotificationController::class, 'markAllAsReadApi'])->name('mark-all-read-api');

        // ===== WEB PAGES =====
        Route::get('/', [NotificationController::class, 'index'])->name('index');

        // ===== DINAMIS (letakkan terakhir) =====
        Route::post('{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
            ->whereNumber('notification')
            ->name('mark-as-read');

        Route::post('{notification}/mark-as-reviewed', [NotificationController::class, 'markAsReviewed'])
            ->whereNumber('notification')
            ->name('mark-as-reviewed'); // konsisten kebab-case
    
        Route::get('{notification}', [NotificationController::class, 'show'])
            ->whereNumber('notification')
            ->name('show');

        Route::delete('{notification}', [NotificationController::class, 'destroy'])
            ->whereNumber('notification')
            ->name('destroy');

        Route::delete('{notification}/api', [NotificationController::class, 'destroyApi'])
            ->whereNumber('notification')
            ->name('destroy.api');
    });

// ======================================================================
// OWNER DASHBOARD & MANUAL INPUT MONITORING
// ======================================================================
Route::prefix('owner')
    ->name('owner.')
    ->middleware(['auth'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manual Input Monitoring
        Route::prefix('manual-inputs')
            ->name('manual-inputs.')
            ->group(function () {
                Route::get('/', [ManualInputController::class, 'index'])->name('index');
                Route::get('/summary', [ManualInputController::class, 'summary'])->name('summary');
                Route::get('/{sale}', [ManualInputController::class, 'show'])->name('show');
            });

        // Alias untuk notifications (agar owner.notifications.* juga tersedia)
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
            ->whereNumber('notification')
            ->name('notifications.show');
    });
