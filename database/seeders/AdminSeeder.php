<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ffinsta.com'],
            [
                'name'          => 'Administrator',
                'password'      => bcrypt('Admin@123456'),
                'role'          => 'admin',
                'points'        => 0,
                'referral_code' => strtoupper(Str::random(8)),
                'is_suspended'  => false,
                'permissions'   => ['users', 'admins', 'orders', 'instagram', 'coupons', 'payments', 'posts', 'faqs', 'logs', 'settings'],
            ]
        );
    }
}
