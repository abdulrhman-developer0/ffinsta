<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\InstagramAccountController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\FaqController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', DashboardController::class)->name('dashboard');

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::patch('/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('suspend');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/adjust-points', [UserController::class, 'adjustPoints'])->name('adjust-points');
        Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/completed', [OrderController::class, 'completed'])->name('completed');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::patch('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::post('/{order}/activate', [OrderController::class, 'activate'])->name('activate');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/toggle-priority', [OrderController::class, 'togglePriority'])->name('toggle-priority');
    });

    // Instagram Accounts
    Route::prefix('instagram')->name('instagram.')->group(function () {
        Route::get('/', [InstagramAccountController::class, 'index'])->name('index');
        Route::get('/{account}', [InstagramAccountController::class, 'show'])->name('show');
        Route::patch('/{account}/status', [InstagramAccountController::class, 'updateStatus'])->name('status');
    });

    // FAQs
    Route::resource('faqs', FaqController::class);

    // Blog Posts
    Route::post('posts/upload-editor-media', [\App\Http\Controllers\Admin\PostController::class, 'uploadEditorMedia'])->name('posts.upload-media');
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);

    // Coupons
    Route::resource('coupons', CouponController::class);

    // Point Payments Logs & Approval
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/approve', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'reject'])->name('payments.reject');

    // Activity Logs
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Language switcher (accessible to all)
Route::get('/lang/{locale}', function (string $locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('lang.switch');
