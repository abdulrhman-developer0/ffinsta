<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingService     $settingService,
        protected ActivityLogService $activityLogService
    ) {}

    public function index()
    {
        $settings = $this->settingService->all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'                  => ['sometimes', 'string', 'max:100'],
            'whatsapp_number'            => ['sometimes', 'nullable', 'string', 'max:50'],
            'site_logo'                  => ['sometimes', 'nullable', 'image', 'max:2048'],
            'site_favicon'               => ['sometimes', 'nullable', 'image', 'max:1024'],
            'points_per_follow'          => ['sometimes', 'integer', 'min:1'],
            'min_points_to_order'        => ['sometimes', 'integer', 'min:0'],
            'registration_enabled'       => ['sometimes', 'boolean'],
            'coupons_enabled'            => ['sometimes', 'boolean'],
            'referrals_enabled'          => ['sometimes', 'boolean'],
            'referral_bonus_points'      => ['sometimes', 'integer', 'min:0'],
            'default_language'           => ['sometimes', 'in:en,ar'],
            'task_lock_minutes'          => ['sometimes', 'integer', 'min:1'],
            'max_tasks_per_hour'         => ['sometimes', 'integer', 'min:1'],
            'daily_task_limit'           => ['sometimes', 'integer', 'min:1'],
            'max_instagram_accounts'     => ['sometimes', 'integer', 'min:1'],
            'notification_email_enabled' => ['sometimes', 'boolean'],
            'sms_payment_store_id'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'sms_payment_store_key'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'sms_payment_api_key'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'points_per_usd'             => ['sometimes', 'integer', 'min:1'],
            'binance_pay_id'             => ['sometimes', 'nullable', 'string', 'max:100'],
            'binance_qr_code'            => ['sometimes', 'nullable', 'image', 'max:2048'],
            'binance_api_key'            => ['sometimes', 'nullable', 'string', 'max:255'],
            'binance_api_secret'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'rapidapi_key'               => ['sometimes', 'nullable', 'string', 'max:255'],
            'rapidapi_host'              => ['sometimes', 'nullable', 'string', 'max:255'],
            'seo_title'                  => ['sometimes', 'nullable', 'string', 'max:255'],
            'seo_description'            => ['sometimes', 'nullable', 'string', 'max:1000'],
            'seo_keywords'               => ['sometimes', 'nullable', 'string', 'max:500'],
            'seo_og_image'               => ['sometimes', 'nullable', 'image', 'max:2048'],
            'seo_custom_head'            => ['sometimes', 'nullable', 'string'],
            'geo_platform_definition'    => ['sometimes', 'nullable', 'string'],
            'geo_key_features'           => ['sometimes', 'nullable', 'string'],
            'geo_target_audience'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'geo_schema_json'            => ['sometimes', 'nullable', 'string'],
        ]);

        $data = $request->except(['_token', '_method', 'site_logo', 'site_favicon', 'binance_qr_code', 'seo_og_image']);

        // Handle boolean checkboxes (unchecked = not in request = 0)
        foreach (['registration_enabled', 'coupons_enabled', 'referrals_enabled', 'notification_email_enabled', 'auto_approve_orders'] as $bool) {
            $data[$bool] = $request->boolean($bool) ? '1' : '0';
        }

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('logos', 'public');
            $data['site_logo'] = '/storage/' . $path;
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('favicons', 'public');
            $data['site_favicon'] = '/storage/' . $path;
        }

        // Handle Binance QR Code upload
        if ($request->hasFile('binance_qr_code')) {
            $path = $request->file('binance_qr_code')->store('binance', 'public');
            $data['binance_qr_code'] = '/storage/' . $path;
        }

        // Handle SEO OG Image upload
        if ($request->hasFile('seo_og_image')) {
            $path = $request->file('seo_og_image')->store('seo', 'public');
            $data['seo_og_image'] = '/storage/' . $path;
        }

        foreach ($data as $key => $value) {
            $this->settingService->set($key, $value);
        }

        $this->activityLogService->log('update_settings', 'Updated system settings');

        return back()->with('success', __('Settings saved successfully.'));
    }
}
