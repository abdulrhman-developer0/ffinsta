<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\InstagramAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchInstagramProfilePictureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\InstagramScraperService $scraperService): void
    {
        // 1. Check if order already has a profile picture
        if ($this->order->profile_picture_url) {
            return;
        }

        $username = $this->order->instagram_username;

        $result = $scraperService->fetchProfilePicture($username);

        if (isset($result['success']) && $result['success'] === true && !empty($result['image'])) {
            $this->order->update(['profile_picture_url' => $result['image']]);
        } else {
            Log::error('FetchInstagramProfilePictureJob failed: ' . ($result['message'] ?? 'Unknown error'));
        }
    }
}
