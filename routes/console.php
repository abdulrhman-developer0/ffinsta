<?php

use App\Console\Commands\ReleaseExpiredTasks;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Release expired follow tasks every 15 minutes
Schedule::command(ReleaseExpiredTasks::class)->everyFifteenMinutes();

// Check pending Binance Pay transactions every minute
Schedule::command(\App\Console\Commands\VerifyPendingBinancePayments::class)->everyMinute();

// Check pending Vodafone Cash transactions every minute
Schedule::command(\App\Console\Commands\VerifyPendingVodafonePayments::class)->everyMinute();
