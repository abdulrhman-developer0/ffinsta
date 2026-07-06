@props(['title' => null, 'header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      x-data
      :class="$store.theme.dark ? 'dark' : ''"
      class="no-transition h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? config('app.name') . ' Dashboard' }}">

    @if(config('settings.site_favicon'))
        <link rel="icon" type="image/png" href="{{ config('settings.site_favicon') }}">
    @elseif(config('settings.site_logo'))
        <link rel="icon" type="image/png" href="{{ config('settings.site_logo') }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-surface"
      x-init="$nextTick(() => document.documentElement.classList.remove('no-transition'))">

<div class="flex h-screen overflow-hidden">

    <!-- ===================== SIDEBAR ===================== -->
    <aside id="sidebar"
           :class="$store.sidebar.open ? 'translate-x-0' : (document.dir === 'rtl' ? 'translate-x-full' : '-translate-x-full')"
           class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-40 w-64 flex flex-col transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
           style="background-color: var(--bg-sidebar); border-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 1px solid var(--border-color);">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-5 flex-shrink-0" style="border-bottom: 1px solid var(--border-color);">
            @if(config('settings.site_logo'))
                <img src="{{ config('settings.site_logo') }}" alt="{{ config('app.name') }}" class="h-8 object-contain">
            @else
                <div class="w-9 h-9 rounded-xl bg-brand-500 flex items-center justify-center shadow-glow flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </div>
            @endif
            @if(!config('settings.site_logo'))
                <div class="min-w-0">
                    <p class="font-bold text-sm text-primary truncate">{{ config('app.name') }}</p>
                    <p class="text-xs text-muted">{{ __('User Panel') }}</p>
                </div>
            @endif
            <button @click="$store.sidebar.close()" class="lg:hidden ml-auto p-1 rounded-lg text-secondary hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Points Balance Banner -->
        <a href="{{ route('user.purchase-points.index') }}" class="block mx-3 mt-3 rounded-xl px-4 py-3 bg-brand-500 bg-opacity-10 dark:bg-brand-900/30 border border-brand-200 dark:border-brand-800 hover:bg-brand-500/20 transition group">
            <p class="text-xs text-muted">{{ __('Your Balance') }}</p>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-xl font-bold text-brand-600 dark:text-brand-400">
                    {{ number_format(auth()->user()->points) }}
                    <span class="text-sm font-normal">{{ __('pts') }}</span>
                </p>
                <div class="w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm text-lg font-medium leading-none pb-0.5" title="{{ __('Buy Points') }}">
                    +
                </div>
            </div>
        </a>

        <!-- Nav links -->
        <nav class="flex-1 overflow-y-auto scrollbar-thin px-3 py-4 space-y-1">
            <div class="px-2 mb-6">
                <a href="{{ route('user.purchase-points.index') }}" class="btn-primary w-full flex items-center justify-center gap-2 py-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Buy Points') }}
                </a>
            </div>

            <p class="px-4 mb-2 text-xs font-semibold uppercase tracking-widest text-muted">{{ __('Menu') }}</p>

            <a href="{{ route('user.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('Dashboard') }}
            </a>

            <a href="{{ route('user.purchase-points.index') }}"
               class="sidebar-link {{ request()->routeIs('user.purchase-points*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Buy Points') }}
            </a>

            <a href="{{ route('user.earn.index') }}"
               class="sidebar-link {{ request()->routeIs('user.earn*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Earn Points') }}
            </a>

            <a href="{{ route('user.orders.index') }}"
               class="sidebar-link {{ request()->routeIs('user.orders*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ __('My Orders') }}
            </a>

            <a href="{{ route('user.instagram.index') }}"
               class="sidebar-link {{ request()->routeIs('user.instagram*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                </svg>
                {{ __('My Accounts') }}
            </a>

            <a href="{{ route('user.points.history') }}"
               class="sidebar-link {{ request()->routeIs('user.points*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ __('Points History') }}
            </a>

            <a href="{{ route('user.coupons.index') }}"
               class="sidebar-link {{ request()->routeIs('user.coupons*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ __('Coupons') }}
            </a>

            <a href="{{ route('user.referral') }}"
               class="sidebar-link {{ request()->routeIs('user.referral*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('Referrals') }}
            </a>

            <a href="{{ route('user.notifications') }}"
               class="sidebar-link {{ request()->routeIs('user.notifications*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{ __('Notifications') }}
                @php $unread = auth()->user()->notifications()->where('is_read', false)->count() @endphp
                @if($unread > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                        {{ $unread > 99 ? '99+' : $unread }}
                    </span>
                @endif
            </a>
        </nav>

        <!-- User profile + actions at bottom -->
        <div class="flex-shrink-0 px-3 py-4 space-y-1" style="border-top: 1px solid var(--border-color);">
            <a href="{{ route('user.profile') }}"
               class="sidebar-link {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
                <div class="w-7 h-7 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-primary truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                </div>
            </a>

            <!-- Theme toggle -->
            <div class="flex items-center justify-between px-4 py-2">
                <span class="text-xs text-muted">{{ __('Theme') }}</span>
                <div class="flex items-center gap-1 bg-surface-3 rounded-lg p-0.5">
                    <button @click="$store.theme.setLight()"
                            :class="!$store.theme.dark ? 'bg-white dark:bg-dark-700 shadow text-brand-500' : 'text-muted'"
                            class="p-1.5 rounded-md transition-all" title="{{ __('Light') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <button @click="$store.theme.setDark()"
                            :class="$store.theme.dark ? 'bg-dark-700 shadow text-brand-400' : 'text-muted'"
                            class="p-1.5 rounded-md transition-all" title="{{ __('Dark') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </aside>

    <!-- Sidebar overlay (mobile) -->
    <div x-show="$store.sidebar.open"
         @click="$store.sidebar.close()"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"
         aria-hidden="true"></div>

    <!-- ===================== MAIN CONTENT ===================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top bar -->
        <header class="flex-shrink-0 h-16 flex items-center gap-3 px-4 lg:px-6"
                style="background-color: var(--bg-card); border-bottom: 1px solid var(--border-color);">

            <button @click="$store.sidebar.toggle()"
                    class="btn-icon text-secondary hover:text-primary lg:hidden flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex-1 min-w-0">
                @isset($header)
                    <div class="text-base font-semibold text-primary truncate">{{ $header }}</div>
                @endisset
            </div>

            <div class="flex items-center gap-2 flex-shrink-0" dir="ltr">
                <!-- Language switcher -->
                <div class="hidden sm:flex gap-1 bg-surface-3 rounded-lg p-0.5">
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-brand-500 text-white' : 'text-muted hover:text-primary' }}">EN</a>
                    <a href="{{ route('lang.switch', 'ar') }}"
                       class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors {{ app()->getLocale() === 'ar' ? 'bg-brand-500 text-white' : 'text-muted hover:text-primary' }}">AR</a>
                </div>

                @livewire('notification-dropdown')

                <button @click="$store.theme.toggle()"
                        class="btn-icon text-secondary hover:text-primary"
                        :title="$store.theme.dark ? '{{ __('Light Mode') }}' : '{{ __('Dark Mode') }}'">
                    <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto scrollbar-thin p-4 lg:p-6 bg-surface-2">
            @if(session('success'))
                <div class="alert-success mb-4 animate-slide-up" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-error mb-4 animate-slide-up" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

@include('components.toast')

@livewireScripts
@stack('scripts')

<script>
    window.addEventListener('load', () => {
        document.documentElement.classList.remove('no-transition');
    });
</script>
</body>
</html>
