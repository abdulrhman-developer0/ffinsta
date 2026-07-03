<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\InstagramAccountController;
use App\Http\Controllers\User\EarnPointsController;
use App\Http\Controllers\User\CouponController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ReferralController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PointsController;
use App\Http\Controllers\User\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'suspended'])->prefix('dashboard')->name('user.')->group(function () {

    // Dashboard
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/leave-impersonation', [\App\Http\Controllers\Admin\UserController::class, 'leaveImpersonation'])->name('leave-impersonation');

    // Orders
    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);

    // Instagram Accounts
    Route::prefix('instagram')->name('instagram.')->group(function () {
        Route::post('/verify', [InstagramAccountController::class, 'verifyProfile'])->name('verify');
        Route::get('/oauth', [InstagramAccountController::class, 'oauthRedirect'])->name('oauth.redirect');
        Route::get('/oauth/callback', [InstagramAccountController::class, 'oauthCallback'])->name('oauth.callback');
        Route::get('/', [InstagramAccountController::class, 'index'])->name('index');
        Route::get('/create', [InstagramAccountController::class, 'create'])->name('create');
        Route::post('/', [InstagramAccountController::class, 'store'])->name('store');
        Route::get('/{account}/edit', [InstagramAccountController::class, 'edit'])->name('edit');
        Route::patch('/{account}', [InstagramAccountController::class, 'update'])->name('update');
        Route::delete('/{account}', [InstagramAccountController::class, 'destroy'])->name('destroy');
        Route::post('/{account}/set-default', [InstagramAccountController::class, 'setDefault'])->name('set-default');
    });

    // Earn Points (Follow Tasks)
    Route::prefix('earn')->name('earn')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\EarnPointsController::class, 'index'])->name('.index');
        Route::post('/claim/{order}', [\App\Http\Controllers\User\EarnPointsController::class, 'claim'])->name('.claim');
        Route::post('/claim-init/{order}', [\App\Http\Controllers\User\EarnPointsController::class, 'claimAndInit'])->name('.claim_and_init');
        Route::post('/complete/{task}', [\App\Http\Controllers\User\EarnPointsController::class, 'complete'])->name('.complete');
        Route::get('/history', [\App\Http\Controllers\User\EarnPointsController::class, 'history'])->name('.history');
        Route::post('/history/{id}/check-status', [\App\Http\Controllers\User\EarnPointsController::class, 'checkStatus'])->name('.check_status');
    });

    // Points History
    Route::get('/points-history', [PointsController::class, 'index'])->name('points.history');

    // Purchase Points
    Route::prefix('purchase-points')->name('purchase-points.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/initiate', [PaymentController::class, 'initiate'])->name('initiate');
    });

    // Coupons
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/redeem', [CouponController::class, 'index'])->name('index');
        Route::post('/redeem', [CouponController::class, 'redeem'])->name('redeem');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('');
        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('.read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('.read-all');
    });

    // Referrals
    Route::get('/referral', [ReferralController::class, 'index'])->name('referral');

    // Profile
    Route::prefix('profile')->name('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('');
        Route::patch('/', [ProfileController::class, 'update'])->name('.update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('.password');
        Route::post('/logout-all', [ProfileController::class, 'logoutAll'])->name('.logout-all');
    });
});
