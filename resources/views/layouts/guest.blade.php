<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      x-data
      :class="$store.theme.dark ? 'dark' : ''"
      class="no-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? __('auth.meta_description') }}">

    <!-- Favicon -->
    @if(config('settings.site_favicon'))
        <link rel="icon" type="image/png" href="{{ config('settings.site_favicon') }}">
    @elseif(config('settings.site_logo'))
        <link rel="icon" type="image/png" href="{{ config('settings.site_logo') }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-surface flex items-center justify-center p-4"
      x-init="
        // Remove no-transition class after theme is applied to avoid flash
        $nextTick(() => document.documentElement.classList.remove('no-transition'))
      ">

    <!-- Background decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10" aria-hidden="true">
        <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-brand-500/10 dark:bg-brand-500/5 blur-[120px] animate-glow-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[450px] h-[450px] rounded-full bg-purple-500/10 dark:bg-purple-500/5 blur-[100px] animate-glow-pulse" style="animation-delay: 3s"></div>
    </div>

    <!-- Theme toggle (top-right) -->
    <div class="fixed top-4 right-4 z-50" dir="ltr">
        <button @click="$store.theme.toggle()"
                class="btn-icon bg-card border border-subtle text-secondary hover:text-primary focus:ring-brand-400"
                :title="$store.theme.dark ? '{{ __('Switch to Light Mode') }}' : '{{ __('Switch to Dark Mode') }}'">
            <!-- Sun icon (light mode) -->
            <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <!-- Moon icon (dark mode) -->
            <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>
    </div>

    <!-- Language switcher (top-left) -->
    <div class="fixed top-4 left-4 z-50" dir="ltr">
        <div class="flex gap-1 bg-card border border-subtle rounded-xl p-1">
            <a href="{{ route('lang.switch', 'en') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-brand-500 text-white' : 'text-secondary hover:text-primary' }}">
                EN
            </a>
            <a href="{{ route('lang.switch', 'ar') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ app()->getLocale() === 'ar' ? 'bg-brand-500 text-white' : 'text-secondary hover:text-primary' }}">
                AR
            </a>
        </div>
    </div>

    <!-- Auth card -->
    <div class="w-full max-w-md relative z-10 animate-slide-up flex flex-col items-center">
        <!-- Logo / App name -->
        <div class="text-center mb-8">
            <a href="/" class="flex flex-col items-center group">
                @if(config('settings.site_logo'))
                    <img src="{{ config('settings.site_logo') }}" alt="{{ config('app.name') }}" class="h-12 object-contain mb-3 transition-transform group-hover:scale-105 duration-300">
                @else
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-500 to-purple-600 flex items-center justify-center mx-auto mb-3 shadow-glow transition-transform group-hover:scale-105 duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </div>
            @endif
        </a>
        @if(!config('settings.site_logo'))
            <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white mt-3">{{ config('app.name') }}</h1>
        @endif
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider mt-1">{{ __('Instagram Follow Exchange') }}</p>
        </div>

        <!-- Card slot (Glassmorphism card wrapper) -->
        <div class="w-full bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/50 p-8 rounded-[2rem] shadow-2xl">
            {{ $slot }}
        </div>
    </div>

    <!-- Toast notifications -->
    @include('components.toast')

    @livewireScripts
    @stack('scripts')

    <script>
        // Remove no-transition after first paint to enable smooth transitions
        window.addEventListener('load', () => {
            document.documentElement.classList.remove('no-transition');
        });
    </script>
</body>
</html>
