<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            ['key' => 'site_name',                 'value' => 'FFInsta',   'group' => 'general'],
            ['key' => 'site_logo',                 'value' => '',          'group' => 'general'],
            ['key' => 'default_language',          'value' => 'en',        'group' => 'general'],

            // Registration & Auth
            ['key' => 'registration_enabled',      'value' => '1',         'group' => 'auth'],

            // Points
            ['key' => 'points_per_follow',         'value' => '10',        'group' => 'points'],
            ['key' => 'min_points_to_order',       'value' => '100',       'group' => 'points'],
            ['key' => 'referral_bonus_points',     'value' => '50',        'group' => 'points'],

            // Coupons & Referrals
            ['key' => 'coupons_enabled',           'value' => '1',         'group' => 'features'],
            ['key' => 'referrals_enabled',         'value' => '1',         'group' => 'features'],

            // Tasks
            ['key' => 'task_lock_minutes',         'value' => '30',        'group' => 'tasks'],
            ['key' => 'max_tasks_per_hour',        'value' => '20',        'group' => 'tasks'],
            ['key' => 'max_instagram_accounts',    'value' => '5',         'group' => 'tasks'],

            // Notifications
            ['key' => 'notification_email_enabled', 'value' => '0',        'group' => 'notifications'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
