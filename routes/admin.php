<?php

use App\Http\Controllers\Admin\AdminController;
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

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');

    // Users
    Route::prefix('users')->middleware('admin_permission:users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::patch('/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('suspend');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/adjust-points', [UserController::class, 'adjustPoints'])->name('adjust-points');
        Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
    });

    // Admins
    Route::resource('admins', AdminController::class)->middleware('admin_permission:admins')->except(['show']);

    // Orders
    Route::prefix('orders')->middleware('admin_permission:orders')->name('orders.')->group(function () {
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
    Route::prefix('instagram')->middleware('admin_permission:instagram')->name('instagram.')->group(function () {
        Route::get('/', [InstagramAccountController::class, 'index'])->name('index');
        Route::get('/{account}', [InstagramAccountController::class, 'show'])->name('show');
        Route::patch('/{account}/status', [InstagramAccountController::class, 'updateStatus'])->name('status');
    });

    // FAQs
    Route::resource('faqs', FaqController::class)->middleware('admin_permission:faqs');

    // Blog Posts
    Route::post('posts/upload-editor-media', [\App\Http\Controllers\Admin\PostController::class, 'uploadEditorMedia'])->middleware('admin_permission:posts')->name('posts.upload-media');
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->middleware('admin_permission:posts');

    // Coupons
    Route::resource('coupons', CouponController::class)->middleware('admin_permission:coupons');

    // Point Payments Logs & Approval
    Route::middleware('admin_permission:payments')->group(function () {
        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/approve', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'reject'])->name('payments.reject');
    });

    // Activity Logs
    Route::get('/logs', [ActivityLogController::class, 'index'])->middleware('admin_permission:logs')->name('logs.index');

    // Settings
    Route::middleware('admin_permission:settings')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

// Language switcher (accessible to all)
Route::get('/lang/{locale}', function (string $locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('lang.switch');
