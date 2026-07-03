<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());
header('Content-Type: application/json');

// Simulate the query for user 2
$user_id = 2;
$completedUsernames = \App\Models\TaskCompletion::where('task_completions.user_id', $user_id)
    ->whereIn('task_completions.status', ['pending', 'verified'])
    ->join('follow_tasks', 'task_completions.task_id', '=', 'follow_tasks.id')
    ->join('orders', 'follow_tasks.requester_order_id', '=', 'orders.id')
    ->pluck('orders.instagram_username');

$assignedUsernames = \App\Models\FollowTask::where('follow_tasks.status', 'assigned')
    ->where('follow_tasks.assigned_user_id', $user_id)
    ->join('orders', 'follow_tasks.requester_order_id', '=', 'orders.id')
    ->pluck('orders.instagram_username');

$excludeUsernames = $completedUsernames->concat($assignedUsernames)->filter()->unique()->values()->toArray();

$query = \App\Models\Order::where('status', 'active')
    ->where('user_id', '!=', $user_id)
    ->whereRaw('requested_qty > delivered_qty + (SELECT COUNT(*) FROM follow_tasks WHERE follow_tasks.requester_order_id = orders.id AND follow_tasks.status IN ("assigned", "completed"))');

if (!empty($excludeUsernames)) {
    $query->whereNotIn('instagram_username', $excludeUsernames);
}

echo json_encode([
    'sql' => $query->toSql(),
    'bindings' => $query->getBindings(),
    'excludeUsernames' => $excludeUsernames,
    'results' => $query->get()->toArray()
]);
