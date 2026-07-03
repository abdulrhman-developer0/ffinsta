<?php

namespace App\Services;

use App\Models\FollowTask;
use App\Models\Order;
use App\Models\TaskCompletion;
use App\Services\PointsService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    public function __construct(
        protected PointsService       $pointsService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Claim an order slot for a user (dynamically creates the task).
     *
     * @throws \Exception
     */
    public function claimTask(int $orderId, int $userId, int $instagramAccountId): FollowTask
    {
        return DB::transaction(function () use ($orderId, $userId, $instagramAccountId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Must be active
            if ($order->status !== 'active') {
                throw new \Exception(__('This order is no longer active.'));
            }

            // Anti-self-follow: user cannot do tasks for their own orders
            if ($order->user_id === $userId) {
                throw new \Exception(__('You cannot complete tasks for your own orders.'));
            }

            // Check if user already claimed this order or another order for the SAME instagram account
            $existingTask = FollowTask::join('orders', 'follow_tasks.requester_order_id', '=', 'orders.id')
                ->where('follow_tasks.assigned_user_id', $userId)
                ->where('orders.instagram_username', $order->instagram_username)
                ->whereIn('follow_tasks.status', ['assigned', 'completed'])
                ->first();

            if ($existingTask) {
                throw new \Exception(__('You have already followed this account in a previous task.'));
            }

            // Check capacity
            $activeTasks = FollowTask::where('requester_order_id', $orderId)
                ->whereIn('status', ['assigned', 'completed'])
                ->count();
                
            if ($order->delivered_qty + $activeTasks >= $order->requested_qty) {
                throw new \Exception(__('This order has reached its requested quantity and has no available slots.'));
            }

            // Check rate limit
            $maxPerHour = (int) app(SettingService::class)->get('max_tasks_per_hour', 20);
            $completedThisHour = TaskCompletion::where('user_id', $userId)
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($completedThisHour >= $maxPerHour) {
                throw new \Exception(__('You have reached the maximum number of tasks per hour. Please try again later.'));
            }

            $rewardPoints = (int) app(SettingService::class)->get('points_per_follow', 10);

            // Create assigned task dynamically (without initial count fetching here)
            $task = FollowTask::create([
                'requester_order_id'           => $order->id,
                'requester_instagram_username' => $order->instagram_username,
                'assigned_user_id'             => $userId,
                'instagram_account_id'         => $instagramAccountId,
                'reward_points'                => $rewardPoints,
                'status'                       => 'assigned',
            ]);

            return $task;
        });
    }

    /**
     * Mark a task as completed/started-verification by the assigned user.
     * Fetches current account following count, or B user follower count on failure, and sets up verification.
     *
     * @throws \Exception
     */
    public function completeTask(int $taskId, int $userId): TaskCompletion
    {
        return DB::transaction(function () use ($taskId, $userId) {
            $task = FollowTask::lockForUpdate()->findOrFail($taskId);

            // Validate ownership
            if ($task->assigned_user_id !== $userId) {
                throw new \Exception(__('This task was not assigned to you.'));
            }

            if ($task->status !== 'assigned') {
                throw new \Exception(__('This task cannot be completed in its current state.'));
            }

            // Prevent duplicate click within active session
            if ($task->complete_clicked_at !== null) {
                throw new \Exception(__('Verification has already started for this task.'));
            }

            $verificationService = app(\App\Services\VerificationService::class);
            $instagramAccount = $task->instagramAccount;

            $followingCount = null;
            $followerCount = null;
            $verificationType = null;

            // 1. Fetch User A's following count
            if ($instagramAccount) {
                $followingCount = $verificationService->getFollowingCount($instagramAccount);
            }

            // 2. Fetch User B's follower count
            $followerCount = $verificationService->getFollowerCount($task->requester_instagram_username);

            if ($followingCount !== null) {
                $task->initial_following_count = $followingCount;
            }
            if ($followerCount !== null) {
                $task->initial_follower_count = $followerCount;
            }

            if ($followerCount !== null && $followingCount !== null) {
                $verificationType = 'both';
            } elseif ($followerCount !== null) {
                $verificationType = 'follower';
            } elseif ($followingCount !== null) {
                $verificationType = 'following';
            } else {
                $verificationType = 'list';
            }

            $task->verification_type = $verificationType;
            $task->complete_clicked_at = now();
            $task->save();

            // Log completion / start verification state
            $completion = TaskCompletion::create([
                'task_id'              => $taskId,
                'user_id'              => $userId,
                'instagram_account_id' => $task->instagram_account_id,
                'status'               => 'pending',
                'completed_at'         => now(),
            ]);

            // Dispatch background verification job with 10 seconds delay
            \App\Jobs\VerifyFollowTask::dispatch($completion->id)->delay(now()->addSeconds(10));

            return $completion;
        });
    }

    /**
     * Run the verification process for a completion.
     * Returns true if verified, false if failed/still pending.
     */
    public function verifyTaskCompletion(TaskCompletion $completion): bool
    {
        $task = $completion->task;
        $instagramAccount = $completion->instagramAccount;

        if (!$task || !$instagramAccount) {
            return false;
        }

        $followerUsername = $instagramAccount->username;
        $targetUsername = $task->requester_instagram_username;

        $isFollowing = false;
        $verificationMethodUsed = 'None';

        // 1. Delta Check - Follower count (User B)
        if (in_array($task->verification_type, ['follower', 'both']) && $task->initial_follower_count !== null) {
            $verificationService = app(\App\Services\VerificationService::class);
            $currentFollowers = $verificationService->getFollowerCount($targetUsername);
            if ($currentFollowers !== null && $currentFollowers > $task->initial_follower_count) {
                $isFollowing = true;
                $verificationMethodUsed = 'Follower Delta';
            }
        }

        // 2. Delta Check - Following count (User A) - Fallback
        if (!$isFollowing && in_array($task->verification_type, ['following', 'both']) && $task->initial_following_count !== null) {
            $verificationService = app(\App\Services\VerificationService::class);
            $currentFollowing = $verificationService->getFollowingCount($instagramAccount);
            if ($currentFollowing !== null && $currentFollowing > $task->initial_following_count) {
                $isFollowing = true;
                $verificationMethodUsed = 'Following Delta';
            }
        }

        // 3. Fallback - List Check / Scraping
        if (!$isFollowing) {
            try {
                $verificationService = app(\App\Services\VerificationService::class);
                $isFollowing = $verificationService->verifyFollow($followerUsername, $targetUsername);
                if ($isFollowing) {
                    $verificationMethodUsed = 'List API';
                }
            } catch (\Exception $e) {
                Log::warning("List verification failed for task completion {$completion->id}: " . $e->getMessage());
            }
        }

        if ($isFollowing) {
            // Success: Update database
            DB::transaction(function () use ($completion, $task, $verificationMethodUsed) {
                $task->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                $this->pointsService->credit(
                    $completion->user_id,
                    $task->reward_points,
                    'earn',
                    __('Completed follow task for @:username', ['username' => $task->requester_instagram_username]),
                    'follow_task',
                    $task->id
                );

                $order = $task->order()->lockForUpdate()->first();
                $order->increment('delivered_qty');

                if ($order->delivered_qty >= $order->requested_qty) {
                    $order->update(['status' => 'completed']);
                    $this->notificationService->orderCompleted($order->user_id, $order->order_number);
                }

                $completion->update([
                    'status' => 'verified',
                    'verification_stage' => "Verified ($verificationMethodUsed)",
                ]);
            });

            return true;
        }

        return false;
    }


    /**
     * Mark expired assigned tasks as failed to free up the order slots.
     * Run via scheduler.
     */
    public function releaseExpiredTasks(): int
    {
        $lockMinutes = (int) app(SettingService::class)->get('task_lock_minutes', 5);

        $expiredTasks = FollowTask::where('status', 'assigned')
            ->where('updated_at', '<=', now()->subMinutes($lockMinutes))
            ->get();

        if ($expiredTasks->isEmpty()) {
            return 0;
        }

        $taskIds = $expiredTasks->pluck('id')->toArray();

        // Reject any pending completions for these tasks
        \App\Models\TaskCompletion::whereIn('task_id', $taskIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'verification_stage' => 'Failed (Task Expired)',
                'reason' => 'Task verification time expired.'
            ]);

        // Release the tasks
        return FollowTask::whereIn('id', $taskIds)
            ->update([
                'status' => 'failed',
                'assigned_user_id' => null,
                'assigned_at' => null,
                'complete_clicked_at' => null,
                'initial_follower_count' => null,
                'initial_following_count' => null,
                'verification_type' => null,
            ]);
    }
}
