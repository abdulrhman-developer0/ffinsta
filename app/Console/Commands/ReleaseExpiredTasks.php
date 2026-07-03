<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use Illuminate\Console\Command;

class ReleaseExpiredTasks extends Command
{
    protected $signature   = 'tasks:release-expired';
    protected $description = 'Release assigned follow tasks that have exceeded the lock time back to available status';

    public function handle(TaskService $taskService): int
    {
        $released = $taskService->releaseExpiredTasks();

        if ($released > 0) {
            $this->info("Released {$released} expired task(s) back to available.");
        } else {
            $this->line('No expired tasks to release.');
        }

        return Command::SUCCESS;
    }
}
