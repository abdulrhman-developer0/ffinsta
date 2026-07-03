<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(25);

        // Mark all as read on page view
        $this->notificationService->markAllRead(auth()->id());

        return view('user.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $this->notificationService->markRead($notification->id, auth()->id());
        return back();
    }

    public function markAllRead()
    {
        $this->notificationService->markAllRead(auth()->id());
        return back()->with('success', __('All notifications marked as read.'));
    }
}
