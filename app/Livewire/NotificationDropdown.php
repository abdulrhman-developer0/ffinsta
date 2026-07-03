<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $open = false;

    public function getNotificationsProperty()
    {
        return Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getUnreadCountProperty(): int
    {
        return Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function markAllRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function markRead(int $id): void
    {
        Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true]);
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
