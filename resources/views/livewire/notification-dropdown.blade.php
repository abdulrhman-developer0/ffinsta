<div class="relative" x-data="{ open: false }" @click.away="open = false">
    {{-- Bell button --}}
    <button @click="open = !open"
            class="btn-icon text-secondary hover:text-primary relative"
            id="notification-bell-btn"
            aria-label="{{ __('Notifications') }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-dark-800">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 card shadow-xl z-50 overflow-hidden"
         style="transform-origin: top right;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <span class="font-semibold text-sm text-primary">{{ __('Notifications') }}</span>
            @if($this->unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-brand-500 hover:underline">
                    {{ __('Mark all read') }}
                </button>
            @endif
        </div>

        {{-- Notifications list --}}
        <div class="max-h-80 overflow-y-auto scrollbar-thin">
            @forelse($this->notifications as $notif)
                <div wire:key="notif-{{ $notif->id }}"
                     class="px-4 py-3 flex gap-3 cursor-pointer hover:bg-surface-3 transition-colors {{ $notif->is_read ? 'opacity-60' : '' }}"
                     wire:click="markRead({{ $notif->id }})">
                    <div class="w-2 h-2 mt-1.5 rounded-full flex-shrink-0 {{ $notif->is_read ? 'bg-transparent' : 'bg-brand-500' }}"></div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-primary truncate">{{ $notif->title }}</p>
                        <p class="text-xs text-secondary mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                        <p class="text-[10px] text-muted mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {{ __('No notifications yet') }}
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 text-center" style="border-top: 1px solid var(--border-color);">
            <a href="{{ route('user.notifications') }}" class="text-xs text-brand-500 hover:underline">
                {{ __('View all notifications') }}
            </a>
        </div>
    </div>
</div>
