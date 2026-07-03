<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Instagram\InstagramExtendSocialite;
use SocialiteProviders\Instagram\Provider as InstagramProvider;
use GuzzleHttp\RequestOptions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('instagram', \App\Providers\CustomInstagramProvider::class);
        });

        try {
            // Load dynamic SMTP and Instagram settings from database
            $settings = app(\App\Services\SettingService::class);
            
            // Set dynamic App name (fallback to FFINSTA)
            $siteName = $settings->get('site_name', 'FFINSTA');
            config(['app.name' => $siteName]);

            // Load all database settings into config under the 'settings.*' namespace
            foreach ($settings->all() as $key => $value) {
                config(['settings.' . $key => $value]);
            }

            if ($settings->get('smtp_host')) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $settings->get('smtp_host'),
                    'mail.mailers.smtp.port' => $settings->get('smtp_port'),
                    'mail.mailers.smtp.username' => $settings->get('smtp_username'),
                    'mail.mailers.smtp.password' => $settings->get('smtp_password'),
                    'mail.mailers.smtp.encryption' => $settings->get('smtp_encryption'),
                    'mail.from.address' => $settings->get('smtp_from_address', config('mail.from.address')),
                    'mail.from.name' => $settings->get('smtp_from_name', config('app.name')),
                ]);
            }

            // Load dynamic Instagram OAuth settings
            config([
                'services.instagram.client_id' => $settings->get('instagram_client_id', env('INSTAGRAM_CLIENT_ID')),
                'services.instagram.client_secret' => $settings->get('instagram_client_secret', env('INSTAGRAM_CLIENT_SECRET')),
                'services.instagram.redirect' => str_starts_with(config('app.url', ''), 'https://') ? secure_url('/dashboard/instagram/oauth/callback') : url('/dashboard/instagram/oauth/callback'),
            ]);
        } catch (\Throwable $e) {
            // Database might not be migrated or reachable yet; safely ignore
        }
    }
}
