<?php

namespace App\Jobs;

use App\Models\TaskCompletion;
use App\Models\User;
use App\Services\PointsService;
use App\Services\VerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyFollowTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $taskCompletionId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(VerificationService $verificationService, PointsService $pointsService): void
    {
        $completion = TaskCompletion::with(['task.order', 'instagramAccount'])->find($this->taskCompletionId);

        if (!$completion || $completion->status !== 'pending') {
            return;
        }

        $completion->increment('verification_attempts');
        $attempt = $completion->verification_attempts;
        $completion->update(['verification_stage' => "Pending Verification - Attempt $attempt"]);

        try {
            $taskService = app(\App\Services\TaskService::class);
            $isFollowing = $taskService->verifyTaskCompletion($completion);

            if ($isFollowing) {
                Log::info("TaskCompletion {$this->taskCompletionId} verified successfully via background job.");
            } else {
                // Did not follow.
                if ($attempt < 4) {
                    $completion->update(['verification_stage' => "Pending Verification - Retrying (Wait 1m)"]);
                    $this->release(60);
                } else {
                    $this->handleFakeFollow($completion);
                }
            }
        } catch (\Exception $e) {
            // Verification methods failed due to API errors, scraping blocks, etc.
            Log::error("TaskCompletion {$this->taskCompletionId} verification completely failed (Attempt $attempt): " . $e->getMessage());
            
            if ($attempt < 4) {
                $completion->update(['verification_stage' => "Pending Verification - Retrying (Wait 1m)"]);
                $this->release(60);
            } else {
                $completion->update([
                    'status' => 'rejected',
                    'verification_stage' => 'Failed',
                    'reason' => 'Verification failed after multiple attempts.'
                ]);
                $completion->task->update([
                    'status' => 'failed',
                    'completed_at' => null,
                    'instagram_account_id' => null,
                    'initial_follower_count' => null,
                    'initial_following_count' => null,
                    'verification_type' => null,
                    'complete_clicked_at' => null,
                ]);
            }
        }
    }

    protected function handleFakeFollow(TaskCompletion $completion): void
    {
        Log::warning("No follow detected for TaskCompletion {$this->taskCompletionId} after 4 attempts. Failing task.");

        $completion->update([
            'status' => 'rejected',
            'verification_stage' => 'Failed',
            'reason' => 'We could not find any task completion activity from your account.'
        ]);
        
        $task = $completion->task;

        // Mark the task as failed so user can try again
        $task->update([
            'status' => 'failed',
            'completed_at' => null,
            'instagram_account_id' => null,
            'initial_follower_count' => null,
            'initial_following_count' => null,
            'verification_type' => null,
            'complete_clicked_at' => null,
        ]);
    }
}
