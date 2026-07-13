<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingService;

class LocaleMiddleware
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = $this->settingService->get(
            'default_language',
            config('app.locale', 'en')
        );

        $locale = session('locale', $defaultLocale);

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = $defaultLocale;
        }

        App::setLocale($locale);

        return $next($request);
    }
}