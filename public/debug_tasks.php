<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());
header('Content-Type: application/json');
echo json_encode(\App\Models\FollowTask::whereIn('requester_order_id', [27, 28])->get()->toArray());
