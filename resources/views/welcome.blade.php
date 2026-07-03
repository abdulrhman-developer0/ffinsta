<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      x-data
      :class="$store.theme.dark ? 'dark' : ''"
      class="no-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('settings.seo_title') ?: config('app.name') . ' — ' . __('Grow Your Instagram Followers') }}</title>
    <meta name="description" content="{{ config('settings.seo_description') ?: __('Earn followers the smart way. Place orders, complete tasks, and grow your Instagram presence with our points-based exchange platform.') }}">
    @if(config('settings.seo_keywords'))
        <meta name="keywords" content="{{ config('settings.seo_keywords') }}">
    @endif

    <!-- Open Graph Meta -->
    <meta property="og:title" content="{{ config('settings.seo_title') ?: config('app.name') }}">
    <meta property="og:description" content="{{ config('settings.seo_description') }}">
    @if(config('settings.seo_og_image'))
        <meta property="og:image" content="{{ url(config('settings.seo_og_image')) }}">
    @endif
    <meta property="og:type" content="website">

    <!-- GEO & AI Meta -->
    @if(config('settings.geo_platform_definition'))
        <meta name="ai-context:definition" content="{{ config('settings.geo_platform_definition') }}">
    @endif
    @if(config('settings.geo_target_audience'))
        <meta name="ai-context:audience" content="{{ config('settings.geo_target_audience') }}">
    @endif

    {!! config('settings.seo_custom_head') !!}

    @if(config('settings.geo_schema_json'))
        <script type="application/ld+json">
            {!! config('settings.geo_schema_json') !!}
        </script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Favicon -->
    @if(config('settings.site_favicon'))
        <link rel="icon" type="image/png" href="{{ config('settings.site_favicon') }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface text-primary antialiased overflow-x-hidden"
      x-init="$nextTick(() => document.documentElement.classList.remove('no-transition'))">

<div class="overflow-hidden relative w-full flex flex-col min-h-screen">

    <!-- ===== BACKGROUND GLOW ORBS ===== -->
    <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-brand-500/10 dark:bg-brand-500/5 rounded-full blur-[120px] pointer-events-none animate-glow-pulse"></div>
    <div class="absolute top-[20%] right-1/4 w-[400px] h-[400px] bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-[100px] pointer-events-none animate-glow-pulse" style="animation-delay: 2s"></div>
    <div class="absolute top-[50%] left-10 w-[300px] h-[300px] bg-pink-500/10 dark:bg-pink-500/5 rounded-full blur-[80px] pointer-events-none animate-glow-pulse" style="animation-delay: 4s"></div>

    <!-- ===== NAVBAR ===== -->
    <nav class="fixed top-0 inset-x-0 z-50 h-20 flex items-center px-6 lg:px-12 border-b border-slate-200/60 dark:border-slate-800/60 glass"
         x-data="{ mobileMenuOpen: false }">
        <div class="max-w-6xl w-full mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-3 group">
                @if(config('settings.site_logo'))
                    <img src="{{ config('settings.site_logo') }}" alt="{{ config('app.name') }}" class="h-9 object-contain">
                @else
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-brand-500 to-purple-600 flex items-center justify-center shadow-glow group-hover:scale-105 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </div>
                @endif
                @if(!config('settings.site_logo'))
                    <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-slate-300 bg-clip-text text-transparent">{{ config('app.name') }}</span>
                @endif
            </a>

            <!-- Desktop Actions -->
            <div class="hidden md:flex items-center gap-4">
                <!-- Theme Toggle -->
                <button @click="$store.theme.toggle()" class="w-10 h-10 rounded-xl border border-slate-200/60 dark:border-slate-800/60 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Blog Link -->
                <a href="{{ route('blog.index') }}" class="font-bold text-sm text-slate-600 dark:text-slate-300 hover:text-brand-500 transition-colors hidden sm:block">
                    {{ __('Blog') }}
                </a>

                <!-- Language Switch -->
                <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'ar' : 'en') }}" class="h-10 px-4 rounded-xl border border-slate-200/60 dark:border-slate-800/60 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 font-bold text-sm transition-colors">
                    {{ app()->getLocale() === 'en' ? 'عربي' : 'EN' }}
                </a>

                @auth
                    <a href="{{ route('user.dashboard') }}" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold shadow-glow hover:scale-[1.02] transition-transform">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold shadow-glow hover:scale-[1.02] transition-transform">{{ __('Get Started') }}</a>
                @endauth
            </div>

            <!-- Mobile Drawer Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden w-10 h-10 rounded-xl border border-slate-200/60 dark:border-slate-800/60 flex items-center justify-center text-slate-600 dark:text-slate-300">
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
             class="absolute top-20 inset-x-0 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 p-6 flex flex-col gap-4 shadow-xl z-40 md:hidden">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-secondary">{{ __('Preferences') }}</span>
                <div class="flex gap-2">
                    <button @click="$store.theme.toggle()" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-semibold flex items-center gap-1.5 text-primary bg-slate-50 dark:bg-slate-800/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        Theme
                    </button>
                    <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'ar' : 'en') }}" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-primary bg-slate-50 dark:bg-slate-800/50">
                        {{ app()->getLocale() === 'en' ? 'عربي' : 'EN' }}
                    </a>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-850">
            <a href="{{ route('blog.index') }}" class="btn-secondary w-full py-3 justify-center rounded-xl font-semibold">{{ __('Blog') }}</a>
            @auth
                <a href="{{ route('user.dashboard') }}" class="btn-primary w-full py-3 justify-center rounded-xl font-bold">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn-secondary w-full py-3 justify-center rounded-xl font-semibold">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="btn-primary w-full py-3 justify-center rounded-xl font-bold">{{ __('Get Started') }}</a>
            @endauth
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="relative min-h-screen flex items-center justify-center px-6 pt-28 pb-16 overflow-hidden">
        <div class="max-w-6xl w-full mx-auto grid lg:grid-cols-12 gap-12 lg:gap-8 items-center relative z-10">
            <!-- Left Info Panel -->
            <div class="lg:col-span-7 text-center lg:text-left rtl:lg:text-right flex flex-col justify-center items-center lg:items-start">
                <!-- Glowing Trust Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-brand-200 dark:border-brand-800/60 bg-brand-50/60 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400 text-sm font-semibold mb-6 animate-fade-in shadow-sm">
                    <span class="w-2.5 h-2.5 bg-brand-500 rounded-full animate-pulse"></span>
                    {{ __('100% Organic Instagram Growth') }}
                </div>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 leading-[1.15] text-slate-900 dark:text-white">
                    {{ __('Grow Your') }} 
                    <span class="bg-gradient-to-r from-brand-500 via-purple-500 to-pink-500 bg-clip-text text-transparent animate-gradient-shift font-black block sm:inline">{{ __('Instagram') }}</span> 
                    <br class="hidden sm:inline">{{ __('The Smart Way') }}
                </h1>

                <p class="text-lg sm:text-xl text-secondary max-w-xl mb-10 leading-relaxed">
                    {{ __('Earn points by following others, then use those points to get real followers on your account. No bots. No spam. Just mutual growth.') }}
                </p>

                <!-- Hero Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('register') }}"
                       class="btn-primary text-base px-8 py-3.5 w-full sm:w-auto shadow-glow hover:shadow-xl hover:-translate-y-0.5 transition-all font-bold rounded-2xl flex items-center justify-center gap-2">
                        {{ __('Start Growing Free') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#how-it-works"
                       class="btn-secondary text-base px-7 py-3.5 w-full sm:w-auto font-bold rounded-2xl flex items-center justify-center">
                        {{ __('How It Works') }}
                    </a>
                </div>

                <!-- Live stats grid -->
                <div class="mt-12 grid grid-cols-3 gap-6 sm:gap-10 border-t border-slate-200/50 dark:border-slate-800/50 pt-8 w-full">
                    <div class="text-center lg:text-left rtl:lg:text-right">
                        <p class="text-3xl font-black text-slate-900 dark:text-white">12.4K+</p>
                        <p class="text-xs sm:text-sm text-secondary font-medium uppercase tracking-wider mt-0.5">{{ __('Active Users') }}</p>
                    </div>
                    <div class="text-center lg:text-left rtl:lg:text-right">
                        <p class="text-3xl font-black text-slate-900 dark:text-white">780K+</p>
                        <p class="text-xs sm:text-sm text-secondary font-medium uppercase tracking-wider mt-0.5">{{ __('Follows Delivered') }}</p>
                    </div>
                    <div class="text-center lg:text-left rtl:lg:text-right">
                        <p class="text-3xl font-black text-slate-900 dark:text-white">4.9★</p>
                        <p class="text-xs sm:text-sm text-secondary font-medium uppercase tracking-wider mt-0.5">{{ __('User Rating') }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Interactive Graphic -->
            <div class="lg:col-span-5 flex justify-center items-center animate-float">
                <div x-data="{ points: 120, followersCount: 843, followed: false, campaignPercent: 84, showBonus: false }" 
                     class="relative w-full max-w-sm">
                    <!-- Glow mesh base -->
                    <div class="absolute -inset-4 bg-gradient-to-tr from-brand-500/20 to-purple-600/20 rounded-3xl filter blur-xl opacity-75 -z-10"></div>

                    <!-- CARD 1: Earning simulator -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-5 shadow-2xl relative z-10 transition-transform duration-300 hover:scale-[1.02]">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800/60 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Earn Points') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 rounded-full text-xs font-extrabold shadow-sm border border-amber-100 dark:border-amber-900/30 relative select-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2-1.1 0-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
                                <span x-text="points">120</span> <span class="font-semibold">{{ __('Pts') }}</span>
                                
                                <!-- Float floating +10 Pts -->
                                <span x-show="showBonus"
                                      x-transition:enter="transition ease-out duration-500"
                                      x-transition:enter-start="opacity-0 -translate-y-0"
                                      x-transition:enter-end="opacity-100 -translate-y-8"
                                      x-transition:leave="transition ease-in duration-300"
                                      x-transition:leave-start="opacity-100"
                                      x-transition:leave-end="opacity-0"
                                      class="absolute left-1/2 -translate-x-1/2 text-emerald-500 font-extrabold text-sm z-30">+10 {{ __('Pts') }}</span>
                            </div>
                        </div>

                        <!-- User element to follow -->
                        <div class="flex items-center gap-4 py-2">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-2xl ring-2 ring-brand-500/20 overflow-hidden flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=120" alt="Instagram Profile" class="w-full h-full object-cover">
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-gradient-to-tr from-brand-500 to-purple-600 text-white rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">@travel_guru</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ __('Follow to earn 10 points') }}</p>
                            </div>
                        </div>

                        <!-- Simulation triggers -->
                        <div class="mt-4 flex gap-2">
                            <button @click="if(!followed) { followed = true; showBonus = true; points += 10; setTimeout(() => { showBonus = false; }, 1200); }" 
                                    :class="followed ? 'bg-emerald-500 text-white cursor-default' : 'bg-brand-500 hover:bg-brand-600 text-white shadow-glow'"
                                    class="flex-1 py-3 rounded-2xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 focus:outline-none">
                                <template x-if="!followed">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        {{ __('Follow') }}
                                    </span>
                                </template>
                                <template x-if="followed">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        {{ __('Followed!') }}
                                    </span>
                                </template>
                            </button>
                            <button @click="followed = false; if(followed) { points -= 10; }"
                                    class="px-4 py-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-bold text-xs rounded-2xl transition-colors">
                                {{ __('Skip') }}
                            </button>
                        </div>
                    </div>

                    <!-- CARD 2: Campaigns simulator -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-5 shadow-2xl mt-4 relative z-0 transition-transform duration-300 hover:scale-[1.02] md:-mr-12 md:translate-x-12 rtl:md:-ml-12 rtl:md:-translate-x-12">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800/60 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('My Campaign') }}</span>
                            </div>
                            <span class="text-[11px] text-brand-500 font-bold bg-brand-50 dark:bg-brand-950/20 px-2 py-0.5 rounded-md" x-text="campaignPercent + '% ' + '{{ __('Completed') }}'">84% {{ __('Completed') }}</span>
                        </div>

                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-extrabold text-sm">
                                IG
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">@your_account</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium" x-text="followersCount + ' ' + '{{ __('Followers') }}'">843 Followers</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
                                <span>{{ __('Followers Delivered') }}</span>
                                <span x-text="campaignPercent + '/100'">84/100</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" :style="'width: ' + campaignPercent + '%'"></div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button @click="if(campaignPercent < 100) { campaignPercent += 1; followersCount += 1; } else { campaignPercent = 0; }" 
                                    class="w-full bg-slate-50 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 py-2.5 rounded-2xl text-xs font-bold transition-all border border-slate-200/50 dark:border-slate-800/50 flex items-center justify-center gap-1.5 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-500 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ __('Simulate Incoming Follower') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== LIVE RUNNING FEED TICKER ===== -->
    <div class="relative w-full overflow-hidden py-4 bg-slate-50 dark:bg-slate-900 border-y border-slate-200/60 dark:border-slate-800/60 select-none">
        <!-- Overlay gradients on edges -->
        <div class="absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-surface to-transparent z-10 pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-surface to-transparent z-10 pointer-events-none"></div>

        <div class="flex gap-12 whitespace-nowrap" :class="document.documentElement.dir === 'rtl' ? 'animate-marquee-rtl' : 'animate-marquee-ltr'">
            <!-- List Elements Group 1 -->
            <div class="flex gap-12 items-center shrink-0">
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @alex_travels {{ __('earned 10 points') }}
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    {{ __('New campaign created for') }} @lens_art (+50 followers)
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @fitness_coach {{ __('earned 15 points') }}
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @beauty_by_sara {{ __('earned 10 points') }}
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ __('Campaign completed for') }} @gaming_world (+100 followers)
                </span>
            </div>
            <!-- Duplicate List Elements Group 2 for infinite marquee -->
            <div class="flex gap-12 items-center shrink-0">
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @alex_travels {{ __('earned 10 points') }}
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    {{ __('New campaign created for') }} @lens_art (+50 followers)
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @fitness_coach {{ __('earned 15 points') }}
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @beauty_by_sara {{ __('earned 10 points') }}
                </span>
                <span class="text-slate-300 dark:text-slate-800 font-black">•</span>
                <span class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ __('Campaign completed for') }} @gaming_world (+100 followers)
                </span>
            </div>
        </div>
    </div>

    <!-- ===== HOW IT WORKS ===== -->
    <section id="how-it-works" class="py-28 px-6 relative">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 dark:text-white mb-4">{{ __('How It Works') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg max-w-xl mx-auto">{{ __('Three simple steps to start growing your organic Instagram audience') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                @foreach([
                    [
                        'step' => '01',
                        'title' => 'Create Account',
                        'desc' => 'Register for free and get bonus points to kickstart your journey.',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>'
                    ],
                    [
                        'step' => '02',
                        'title' => 'Earn Points',
                        'desc' => 'Follow other users\' accounts to earn points. Simple, fast, and rewarding.',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    ],
                    [
                        'step' => '03',
                        'title' => 'Get Followers',
                        'desc' => 'Place an order and watch real followers arrive on your Instagram profile.',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
                    ]
                ] as $item)
                    <div class="card-premium-glow rounded-3xl p-8 text-center flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-brand-50 dark:bg-brand-950/20 text-brand-500 flex items-center justify-center mb-6 shadow-sm border border-brand-100/30 dark:border-brand-900/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $item['svg'] !!}
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-brand-500 uppercase tracking-widest mb-3 bg-brand-50 dark:bg-brand-950/20 px-3 py-1 rounded-full">{{ __('Step') }} {{ $item['step'] }}</span>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __($item['title']) }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 leading-relaxed text-sm">{{ __($item['desc']) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== POINTS CALCULATOR SECTION ===== -->
    <section class="py-24 px-6 relative bg-slate-50/50 dark:bg-slate-900/40 border-y border-slate-200/40 dark:border-slate-800/40">
        <div class="max-w-6xl w-full mx-auto grid lg:grid-cols-12 gap-12 items-center">
            <!-- Details -->
            <div class="lg:col-span-6 text-center lg:text-left rtl:lg:text-right">
                <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">{{ __('Calculate Your Growth Potential') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg mb-8 leading-relaxed">
                    {{ __('Determine exactly how much effort is needed to reach your growth goal. Move the slider to see points required, equivalent accounts to follow, and get estimated deliveries instantly.') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('register') }}" class="btn-primary px-8 py-3.5 rounded-2xl font-bold text-sm shadow-glow w-full sm:w-auto text-center">{{ __('Start Earning Points') }}</a>
                </div>
            </div>

            <!-- Interactive Slider Calculator -->
            <div class="lg:col-span-6">
                <div x-data="{ followersWanted: 200, pointsNeeded() { return this.followersWanted * 10; }, tasksNeeded() { return this.followersWanted; } }" 
                     class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-6 sm:p-8 shadow-2xl relative">
                    <div class="absolute -top-3 left-6 px-3 py-1 bg-brand-500 text-white rounded-full text-[10px] font-bold tracking-widest uppercase shadow-md">{{ __('Calculator') }}</div>
                    
                    <div class="space-y-6 mt-2">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm font-bold text-slate-500 dark:text-slate-400">{{ __('Followers Desired') }}</span>
                                <span class="text-xl font-black text-brand-500" x-text="followersWanted + ' ' + '{{ __('Followers') }}'">200 Followers</span>
                            </div>
                            <input type="range" min="20" max="1000" step="10" x-model="followersWanted" class="w-full h-2 bg-slate-200 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-brand-500">
                            <div class="flex justify-between text-[10px] text-slate-400 dark:text-slate-600 font-bold mt-2 select-none">
                                <span>20</span>
                                <span>250</span>
                                <span>500</span>
                                <span>750</span>
                                <span>1000</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-100 dark:border-slate-800/60 pt-6">
                            <div class="bg-slate-50 dark:bg-slate-800/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-800/50 text-center">
                                <span class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Points Required') }}</span>
                                <span class="text-2xl font-black text-slate-900 dark:text-white" x-text="pointsNeeded()">2000</span>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-800/50 text-center">
                                <span class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Equivalent Tasks') }}</span>
                                <span class="text-2xl font-black text-slate-900 dark:text-white" x-text="tasksNeeded()">200</span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-0.5">{{ __('accounts to follow') }}</span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('register') }}" class="btn-primary w-full py-3.5 justify-center shadow-glow text-sm font-bold rounded-2xl">
                                {{ __('Claim These Followers Now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== WHY CHOOSE US / FEATURES ===== -->
    <section class="py-28 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 dark:text-white mb-4">{{ __('Why Choose Us') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg max-w-xl mx-auto">{{ __('Built for organic, high-converting growth, backed by smart verification tools.') }}</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach([
                    [
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.957 11.957 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                        'title' => 'Anti-Fraud Protection',
                        'desc' => 'You cannot follow your own accounts. Our system validates every interaction, ensuring absolute fair exchange between users.',
                        'color' => 'bg-red-50 text-red-500 dark:bg-red-950/20'
                    ],
                    [
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                        'title' => 'Fast Delivery',
                        'desc' => 'Orders are activated instantly. Once your campaign is in place, points are allocated and followers start trickling in within hours.',
                        'color' => 'bg-amber-50 text-amber-500 dark:bg-amber-950/20'
                    ],
                    [
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 8h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Earn Bonus Points',
                        'desc' => 'Share custom referral links, redeem promo codes, and complete tasks to pile up bonus points to accelerate your progression.',
                        'color' => 'bg-emerald-50 text-emerald-500 dark:bg-emerald-950/20'
                    ],
                    [
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>',
                        'title' => 'Dark & Light Mode',
                        'desc' => 'Switch seamlessly between light and dark themes tailored to protect your eyes and match your device styling preferences.',
                        'color' => 'bg-purple-50 text-purple-500 dark:bg-purple-950/20'
                    ],
                    [
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2a2.5 2.5 0 002.5-2.5V14M9 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Arabic & English',
                        'desc' => 'Fully localized for global audiences, including dedicated right-to-left layout alignment for Arabic speaking users.',
                        'color' => 'bg-blue-50 text-blue-500 dark:bg-blue-950/20'
                    ],
                    [
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                        'title' => 'Track Progress',
                        'desc' => 'Log into your profile dashboard to review analytical insights, point transfers, campaign histories, and live order charts.',
                        'color' => 'bg-pink-50 text-pink-500 dark:bg-pink-950/20'
                    ]
                ] as $feature)
                    <div class="card-premium-glow rounded-3xl p-6 text-left rtl:text-right">
                        <div class="w-12 h-12 rounded-xl {{ $feature['color'] }} flex items-center justify-center mb-5 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $feature['svg'] !!}
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-lg mb-2">{{ __($feature['title']) }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ __($feature['desc']) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== ACCORDION FAQ SECTION ===== -->
    <section class="py-24 px-6 relative bg-slate-50/50 dark:bg-slate-900/40 border-y border-slate-200/40 dark:border-slate-800/40">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 dark:text-white mb-4">{{ __('Frequently Asked Questions') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg">{{ __('Have queries? Check our most frequently answered doubts.') }}</p>
            </div>

            <!-- Accordion wrap -->
            <div x-data="{ active: null }" class="space-y-4">
                @foreach($faqs as $faq)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 rounded-2xl overflow-hidden transition-all shadow-sm">
                        <button @click="active === {{ $faq->id }} ? active = null : active = {{ $faq->id }}" 
                                class="w-full px-6 py-5 flex items-center justify-between text-left rtl:text-right text-slate-900 dark:text-white font-bold text-base hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-colors focus:outline-none">
                            <span>{{ $faq->question }}</span>
                            <svg :class="active === {{ $faq->id }} ? 'rotate-180 text-brand-500' : 'text-slate-400'" 
                                 class="w-5 h-5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="active === {{ $faq->id }}" 
                             x-collapse
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 max-h-0"
                             x-transition:enter-end="opacity-100 max-h-screen"
                             class="px-6 pb-5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800/40 pt-4">
                            {{ $faq->answer }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== CALL TO ACTION ===== -->
    <section class="py-28 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-brand-600 via-brand-500 to-purple-600 rounded-3xl p-10 sm:p-16 text-center text-white shadow-2xl relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>

                <h2 class="text-3xl sm:text-5xl font-black mb-4">{{ __('Ready to Grow?') }}</h2>
                <p class="text-brand-100 text-lg mb-8 max-w-lg mx-auto">{{ __('Join thousands of content creators and businesses growing their Instagram organic presence today.') }}</p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-white text-brand-600 font-extrabold text-base hover:bg-brand-50 transition-colors shadow-lg">
                    {{ __('Create Free Account') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="py-12 px-6 border-t border-slate-200/60 dark:border-slate-800/60">
        <div class="max-w-6xl w-full mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Copyright & Logo -->
            <div class="flex flex-col items-center md:items-start gap-2 text-center md:text-left rtl:md:text-right">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-500 flex items-center justify-center shadow-glow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                        </svg>
                    </div>
                    <span class="font-extrabold text-slate-800 dark:text-white">{{ config('app.name') }}</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">&copy; {{ date('Y') }} {{ __('SOFTINGY. All rights reserved.') }}</p>
            </div>

            <!-- Links -->
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-slate-500 dark:text-slate-400 font-semibold">
                <a href="{{ route('legal.privacy') }}" class="hover:text-brand-500 transition-colors">{{ __('Privacy Policy') }}</a>
                <span class="text-slate-300 dark:text-slate-800">•</span>
                <a href="{{ route('legal.terms') }}" class="hover:text-brand-500 transition-colors">{{ __('Terms of Service') }}</a>
                <span class="text-slate-300 dark:text-slate-800">•</span>
                <a href="{{ route('legal.refund') }}" class="hover:text-brand-500 transition-colors">{{ __('Refund Policy') }}</a>
            </div>
        </div>
    </footer>
</div>

    <script>
        window.addEventListener('load', () => {
            document.documentElement.classList.remove('no-transition');
        });
    </script>
</body>
</html>
