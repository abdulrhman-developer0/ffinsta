<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Debugging
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::get('/debug-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) return 'No log file';
    $lines = file($logFile);
    $errorLines = array_filter($lines, function($line) {
        return str_contains($line, 'Instagram OAuth Error') || str_contains($line, 'Exception');
    });
    return implode("", array_slice($errorLines, -50));
});

// Welcome / Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    $faqs = \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->get();
    return view('welcome', compact('faqs'));
})->name('home');

// Profile (Breeze default — keep for compatibility but user panel has its own)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Language Switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Blog Routes
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/tag/{slug}', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.tag');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Legal Pages
Route::get('/privacy-policy', function (\App\Services\SettingService $settings) {
    return view('legal.show', [
        'title' => __('Privacy Policy'),
        'content' => $settings->get('privacy_policy', __('No privacy policy has been configured yet.'))
    ]);
})->name('legal.privacy');

Route::get('/terms-of-service', function (\App\Services\SettingService $settings) {
    return view('legal.show', [
        'title' => __('Terms and Conditions'),
        'content' => $settings->get('terms_conditions', __('No terms and conditions have been configured yet.'))
    ]);
})->name('legal.terms');

Route::get('/refund-policy', function (\App\Services\SettingService $settings) {
    return view('legal.show', [
        'title' => __('Refund Policy'),
        'content' => $settings->get('refund_policy', __('No refund policy has been configured yet.'))
    ]);
})->name('legal.refund');

Route::get('/proxy-image', [\App\Http\Controllers\ImageProxyController::class, 'proxy'])->name('proxy.image');
Route::middleware('auth')->group(function () {
    Route::get('/search-images', [\App\Http\Controllers\ImageSearchController::class, 'search'])->name('search.images');
});
Route::post('/payment/callback', [\App\Http\Controllers\User\PaymentController::class, 'callback'])->name('payment.callback');

// Cron job route for Shared Hosting
Route::get('/cron/run-queue', function (\Illuminate\Http\Request $request) {
    if ($request->token !== 'ffinsta_cron_secure_123') {
        abort(403, 'Unauthorized');
    }

    \Illuminate\Support\Facades\Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--max-time' => 50,
    ]);

    return "Queue worker executed successfully.";
});

// GitHub Deployment Webhook
Route::post('/deploy/webhook', [\App\Http\Controllers\DeployController::class, 'deploy']);

require __DIR__.'/auth.php';
