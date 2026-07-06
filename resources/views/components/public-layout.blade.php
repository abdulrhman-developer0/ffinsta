<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      x-data
      :class="$store.theme.dark ? 'dark' : ''"
      class="no-transition">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    
    @if(isset($meta_description))
        <meta name="description" content="{{ $meta_description }}">
        <meta property="og:description" content="{{ $meta_description }}">
    @endif
    
    @if(isset($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endif
    
    @if(isset($og_image))
        <meta property="og:image" content="{{ $og_image }}">
    @endif
    
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">

    @isset($meta_header)
        {!! $meta_header !!}
    @endisset

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-primary antialiased font-sans flex flex-col min-h-screen"
      x-init="$nextTick(() => document.documentElement.classList.remove('no-transition'))">

    <nav class="fixed top-0 inset-x-0 z-50 h-16 flex items-center px-6 lg:px-12 bg-surface-2 border-b border-slate-200 dark:border-slate-800"
         x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl w-full mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 hover:opacity-80 transition-opacity">
                @if(config('settings.site_logo'))
                    <img src="{{ config('settings.site_logo') }}" alt="{{ config('app.name') }}" class="h-8 object-contain">
                @else
                <div class="w-8 h-8 rounded-xl bg-brand-500 flex items-center justify-center shadow-glow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/></svg>
                </div>
                <span class="font-bold text-primary">{{ config('app.name') }}</span>
                @endif
            </a>
            
            <!-- Desktop Links -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-secondary hover:text-brand-500 transition-colors">{{ __('Home') }}</a>
                <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors">{{ __('Blog') }}</a>
                
                <div class="flex gap-1 bg-surface-3 rounded-lg p-0.5" dir="ltr">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-brand-500 text-white' : 'text-muted hover:text-primary' }}">EN</a>
                    <a href="{{ route('lang.switch', 'ar') }}" class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors {{ app()->getLocale() === 'ar' ? 'bg-brand-500 text-white' : 'text-muted hover:text-primary' }}">AR</a>
                </div>

                <button @click="$store.theme.toggle()" class="btn-icon text-secondary hover:text-primary">
                    <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
            </div>

            <!-- Mobile Drawer Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden btn-icon text-secondary hover:text-primary">
                <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 translate-y-4"
             class="absolute top-16 inset-x-0 bg-surface-2 border-b border-slate-200 dark:border-slate-800 p-6 flex flex-col gap-4 shadow-xl z-40 md:hidden">
            
            <a href="{{ route('home') }}" class="text-base font-semibold text-primary hover:text-brand-500 transition-colors border-b border-slate-100 dark:border-slate-800/40 pb-3">{{ __('Home') }}</a>
            <a href="{{ route('blog.index') }}" class="text-base font-semibold text-brand-500 transition-colors border-b border-slate-100 dark:border-slate-800/40 pb-3">{{ __('Blog') }}</a>
            
            <div class="flex items-center justify-between pt-2">
                <button @click="$store.theme.toggle()" class="flex items-center gap-2 text-sm font-semibold text-secondary">
                    <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <span>{{ __('Theme') }}</span>
                </button>

                <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'ar' : 'en') }}" class="text-sm font-bold text-primary bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                    {{ app()->getLocale() === 'en' ? 'عربي' : 'EN' }}
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-1 pt-16">
        @isset($header)
            {{ $header }}
        @endisset
        {{ $slot }}
    </main>

    <footer class="py-8 px-6 text-center text-sm text-muted border-t border-slate-200 dark:border-slate-800 bg-surface mt-12">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </footer>

    <script>
        window.addEventListener('load', () => {
            document.documentElement.classList.remove('no-transition');
        });
    </script>
</body>
</html>
