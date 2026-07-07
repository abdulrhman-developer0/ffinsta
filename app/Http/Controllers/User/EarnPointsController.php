<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FollowTask;
use App\Models\InstagramAccount;
use App\Services\TaskService;
use Illuminate\Http\Request;

class EarnPointsController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function index()
    {
        $user = auth()->user();

        // Dynamically release expired tasks right before loading to ensure immediate UI update
        app(\App\Services\TaskService::class)->releaseExpiredTasks();

        // User's currently assigned tasks
        $myAssignedTasks = FollowTask::where('status', 'assigned')
            ->where('assigned_user_id', $user->id)
            ->with('order')
            ->get();

        // Orders the user has already interacted with (assigned or completed tasks for)
        $completedUsernames = \App\Models\TaskCompletion::where('task_completions.user_id', $user->id)
            ->whereIn('task_completions.status', ['pending', 'verified'])
            ->join('follow_tasks', 'task_completions.task_id', '=', 'follow_tasks.id')
            ->join('orders', 'follow_tasks.requester_order_id', '=', 'orders.id')
            ->pluck('orders.instagram_username');
            
        $assignedUsernames = \App\Models\FollowTask::where('follow_tasks.status', 'assigned')
            ->where('follow_tasks.assigned_user_id', $user->id)
            ->join('orders', 'follow_tasks.requester_order_id', '=', 'orders.id')
            ->pluck('orders.instagram_username');
            
        $excludeUsernames = $completedUsernames->concat($assignedUsernames)->filter()->unique()->values()->toArray();

        // Get active orders that have available slots and are not excluded
        $availableOrders = collect();
        
        // Only allow new tasks if pending tasks are less than 3
        if ($myAssignedTasks->count() < 3) {
            $query = \App\Models\Order::where('status', 'active')
                ->where('user_id', '!=', $user->id)
                ->whereRaw('requested_qty > delivered_qty + (SELECT COUNT(*) FROM follow_tasks WHERE follow_tasks.requester_order_id = orders.id AND follow_tasks.status IN ("assigned", "completed"))');

            if (!empty($excludeUsernames)) {
                $query->whereNotIn('instagram_username', $excludeUsernames);
            }

            $availableOrders = $query->orderByRaw("priority = 'high' DESC")->inRandomOrder()->limit(5)->get();
        }

        $userAccounts = InstagramAccount::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        $rewardPoints = (int) app(\App\Services\SettingService::class)->get('points_per_follow', 10);
        
        // Stats
        $completedTasksToday = \App\Models\TaskCompletion::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'approved')
            ->count();

        $maxTasksPerHour = (int) app(\App\Services\SettingService::class)->get('max_tasks_per_hour', 20);
        $completedTasksThisHour = \App\Models\TaskCompletion::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('status', 'approved')
            ->count();
            
        $dailyLimit = (int) app(\App\Services\SettingService::class)->get('daily_task_limit', 20);
        $remainingDailyLimit = max(0, $dailyLimit - $completedTasksToday);
        $remainingDailyPoints = $remainingDailyLimit * $rewardPoints;

        // No refresh timer needed anymore, just define an empty variable for the view compatibility if any
        $refreshWaitRemaining = 0;

        return view('user.earn.index', compact(
            'availableOrders', 'myAssignedTasks', 'userAccounts', 'rewardPoints',
            'completedTasksToday', 'dailyLimit', 'remainingDailyLimit', 'remainingDailyPoints',
            'refreshWaitRemaining', 'maxTasksPerHour', 'completedTasksThisHour'
        ));
    }

    public function claim(Request $request, \App\Models\Order $order)
    {
        $request->validate([
            'instagram_account_id' => ['required', 'exists:instagram_accounts,id'],
        ]);

        $account = InstagramAccount::where('id', $request->instagram_account_id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        try {
            $this->taskService->claimTask($order->id, auth()->id(), $account->id);
            return back()->with('success', __('Task claimed! Click "Open Instagram Profile" and then "Verify Task".'));
        } catch (\Exception $e) {
            return back()->withErrors(['task' => $e->getMessage()]);
        }
    }

    public function claimAndInit(Request $request, \App\Models\Order $order)
    {
        $request->validate([
            'instagram_account_id' => ['required', 'exists:instagram_accounts,id'],
        ]);

        $account = InstagramAccount::where('id', $request->instagram_account_id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        try {
            // Claim task
            $task = $this->taskService->claimTask($order->id, auth()->id(), $account->id);
            
            // Instantly fetch counts and start verification timer
            $this->taskService->completeTask($task->id, auth()->id());
            
            // Since this runs in a new tab via target="_blank", redirect away to the IG profile
            return redirect()->away('https://instagram.com/' . urlencode($order->instagram_username));
        } catch (\Exception $e) {
            // On failure, alert and close the new tab. The original tab will reload and the user will see they need to try again.
            $msg = addslashes($e->getMessage());
            return response("<script>alert('Error: {$msg}'); window.close();</script>");
        }
    }

    public function complete(Request $request, FollowTask $task)
    {
        if ($task->assigned_user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $completion = $this->taskService->completeTask($task->id, auth()->id());

            return back()->with([
                'success' => __('Task verification started. Please follow the profile if you haven\'t already.')
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['task' => $e->getMessage()]);
        }
    }

    public function history()
    {
        $completions = auth()->user()->taskCompletions()
            ->with(['task'])
            ->latest()
            ->paginate(20);

        return view('user.earn.history', compact('completions'));
    }

    public function checkStatus($id)
    {
        $completion = auth()->user()->taskCompletions()->findOrFail($id);

        if ($completion->status !== 'pending') {
            return back()->withErrors(['task' => 'This task is not pending verification.']);
        }

        $completion->increment('verification_attempts');
        $attempt = $completion->verification_attempts;
        $completion->update(['verification_stage' => "Manual Verification - Attempt $attempt"]);

        try {
            $isFollowing = $this->taskService->verifyTaskCompletion($completion);

            if ($isFollowing) {
                return back()->with('success', __('Task verified! :pts points added.', ['pts' => $completion->task->reward_points]));
            } else {
                if ($attempt < 4) {
                    $completion->update(['verification_stage' => "Pending Verification - Retrying (Wait 1m)"]);
                    $completion->task->update(['complete_clicked_at' => now()]);
                    return back()->withErrors(['task' => __('Verification still pending. Please wait and try again.')]);
                } else {
                    $completion->update([
                        'status' => 'rejected',
                        'verification_stage' => 'Failed',
                        'reason' => 'We could not find any task completion activity from your account.'
                    ]);
                    
                    // Mark follow task as failed so user can try again
                    $completion->task->update([
                        'status' => 'failed',
                        'completed_at' => null,
                        'instagram_account_id' => null,
                        'initial_follower_count' => null,
                        'initial_following_count' => null,
                        'verification_type' => null,
                        'complete_clicked_at' => null,
                    ]);
                    return back()->withErrors(['task' => 'Verification failed: We could not find any task completion activity from your account.']);
                }
            }
        } catch (\Exception $e) {
            if ($attempt < 4) {
                $completion->update(['verification_stage' => "Pending Verification - Retrying (Wait 1m)"]);
                $completion->task->update(['complete_clicked_at' => now()]);
                return back()->withErrors(['task' => __('Verification system busy. Please wait and try again.')]);
            } else {
                $completion->update([
                    'status' => 'rejected',
                    'verification_stage' => 'Failed',
                    'reason' => 'Verification failed after multiple attempts.'
                ]);
                
                // Mark follow task as failed so user can try again
                $completion->task->update([
                    'status' => 'failed',
                    'completed_at' => null,
                    'instagram_account_id' => null,
                    'initial_follower_count' => null,
                    'initial_following_count' => null,
                    'verification_type' => null,
                    'complete_clicked_at' => null,
                ]);
                return back()->withErrors(['task' => 'Verification completely failed after 4 attempts.']);
            }
        }
    }
}
