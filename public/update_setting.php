<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());
header('Content-Type: text/plain');
\App\Models\Setting::updateOrCreate(['key' => 'task_lock_minutes'], ['value' => '10']);
echo 'Updated to 10';
