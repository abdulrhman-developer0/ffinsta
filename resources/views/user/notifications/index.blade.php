<x-app-layout>
    <x-slot name="title">{{ __('Notifications') }}</x-slot>
    <x-slot name="header">{{ __('Notifications') }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-5 py-3 flex items-center justify-between" style="border-bottom: 1px solid var(--border-color);">
                <h2 class="section-title">{{ __('All Notifications') }}</h2>
                <form method="POST" action="{{ route('user.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs text-brand-500 hover:underline">{{ __('Mark all read') }}</button>
                </form>
            </div>

            @forelse($notifications as $notif)
                <div class="flex gap-4 px-5 py-4 {{ $notif->is_read ? 'opacity-60' : '' }}" style="border-bottom: 1px solid var(--border-subtle);">
                    <div class="w-2 h-2 mt-2 flex-shrink-0 rounded-full {{ $notif->is_read ? 'bg-transparent border border-muted' : 'bg-brand-500' }}"></div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-primary">{{ $notif->title }}</p>
                        <p class="text-sm text-secondary mt-0.5">{{ $notif->message }}</p>
                        <p class="text-xs text-muted mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p>{{ __('No notifications yet') }}</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-4">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-app-layout>
