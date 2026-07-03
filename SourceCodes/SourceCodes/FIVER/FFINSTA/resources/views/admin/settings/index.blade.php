<x-admin-layout>
    <x-slot name="title">{{ __('Settings') }}</x-slot>
    <x-slot name="header">{{ __('System Settings') }}</x-slot>

    <div class="max-w-4xl mx-auto" x-data="{ activeTab: 'general' }">
        
        {{-- Top Navigation Tabs --}}
        <div class="flex overflow-x-auto border-b border-gray-200 dark:border-gray-800 mb-6 pb-px">
            <button @click="activeTab = 'general'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'general'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('General') }}
            </button>
            <button @click="activeTab = 'auth'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'auth', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'auth'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Authentication') }}
            </button>
            <button @click="activeTab = 'points'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'points', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'points'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Points') }}
            </button>
            <button @click="activeTab = 'tasks'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'tasks', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'tasks'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Tasks') }}
            </button>
            <button @click="activeTab = 'features'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'features', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'features'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Features') }}
            </button>
            <button @click="activeTab = 'smtp'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'smtp', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'smtp'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('SMTP') }}
            </button>
            <button @click="activeTab = 'legal'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'legal', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'legal'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Legal') }}
            </button>
            <button @click="activeTab = 'instagram'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'instagram', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'instagram'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Instagram API') }}
            </button>
            <button @click="activeTab = 'payments'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'payments', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'payments'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('Payments') }}
            </button>
            <button @click="activeTab = 'rapidapi'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'rapidapi', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'rapidapi'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('RapidAPI') }}
            </button>
            <button @click="activeTab = 'seo'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'seo', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'seo'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('SEO') }}
            </button>
            <button @click="activeTab = 'geo'" 
                    :class="{'border-brand-500 text-brand-600 dark:text-brand-400': activeTab === 'geo', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'geo'}"
                    class="whitespace-nowrap pb-3 px-4 border-b-2 font-medium text-sm transition-colors duration-150">
                {{ __('GEO') }}
            </button>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="card p-6">
                {{-- General --}}
                <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-4">{{ __('General Settings') }}</h2>
                    <div class="max-w-2xl space-y-4">
                        <div>
                            <label class="form-label">{{ __('Site Name') }}</label>
                            <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="form-input" maxlength="100">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Default Language') }}</label>
                            <select name="default_language" class="form-input">
                                <option value="en" {{ ($settings['default_language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ ($settings['default_language'] ?? 'en') === 'ar' ? 'selected' : '' }}>Arabic</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">{{ __('WhatsApp Support Number') }}</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '201070849236') }}" class="form-input" placeholder="e.g. 201070849236">
                            <p class="text-xs text-muted mt-1">{{ __('Used for the WhatsApp support button in the user sidebar. Include country code without +') }}</p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('Site Logo') }}</label>
                            <input type="file" name="site_logo" class="form-input" accept="image/*">
                            @if(config('settings.site_logo'))
                                <div class="mt-2 p-2 bg-slate-100 dark:bg-slate-800 rounded-xl inline-block border border-slate-200 dark:border-slate-700">
                                    <img src="{{ config('settings.site_logo') }}" alt="Site Logo" class="h-8 object-contain">
                                </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <label class="form-label">{{ __('Site Favicon') }}</label>
                            <input type="file" name="site_favicon" class="form-input" accept="image/*">
                            @if(config('settings.site_favicon'))
                                <div class="mt-2 p-2 bg-slate-100 dark:bg-slate-800 rounded-xl inline-block border border-slate-200 dark:border-slate-700">
                                    <img src="{{ config('settings.site_favicon') }}" alt="Site Favicon" class="w-8 h-8 object-contain">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Authentication --}}
                <div x-show="activeTab === 'auth'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-4">{{ __('Authentication') }}</h2>
                    <div class="max-w-2xl space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="registration_enabled" value="1"
                                   {{ ($settings['registration_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-500 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="font-medium text-primary text-sm">{{ __('Enable Registration') }}</span>
                                <p class="text-xs text-muted">{{ __('Allow new users to register on the platform.') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Points --}}
                <div x-show="activeTab === 'points'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-4">{{ __('Points System') }}</h2>
                    <div class="max-w-2xl space-y-4">
                        <div>
                            <label class="form-label">{{ __('Points Per Follow') }}</label>
                            <input type="number" name="points_per_follow" value="{{ old('points_per_follow', $settings['points_per_follow'] ?? 10) }}" class="form-input" min="1">
                            <p class="text-xs text-muted mt-1">{{ __('Points earned when completing a task, and points spent when requesting a follower.') }}</p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('Minimum Points to Order') }}</label>
                            <input type="number" name="min_points_to_order" value="{{ old('min_points_to_order', $settings['min_points_to_order'] ?? 0) }}" class="form-input" min="0">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Referral Bonus Points') }}</label>
                            <input type="number" name="referral_bonus_points" value="{{ old('referral_bonus_points', $settings['referral_bonus_points'] ?? 50) }}" class="form-input" min="0">
                            <p class="text-xs text-muted mt-1">{{ __('Points awarded to both users when a referral registers successfully.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tasks --}}
                <div x-show="activeTab === 'tasks'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-4">{{ __('Task Settings') }}</h2>
                    <div class="max-w-2xl space-y-4">
                        <div>
                            <label class="form-label">{{ __('Task Lock Duration (minutes)') }}</label>
                            <input type="number" name="task_lock_minutes" value="{{ old('task_lock_minutes', $settings['task_lock_minutes'] ?? 30) }}" class="form-input" min="1">
                            <p class="text-xs text-muted mt-1">{{ __('How long a claimed task is reserved for a user before being released back into the pool.') }}</p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('Max Tasks Per Hour') }}</label>
                            <input type="number" name="max_tasks_per_hour" value="{{ old('max_tasks_per_hour', $settings['max_tasks_per_hour'] ?? 20) }}" class="form-input" min="1">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Max Tasks Per Day') }}</label>
                            <input type="number" name="daily_task_limit" value="{{ old('daily_task_limit', $settings['daily_task_limit'] ?? 20) }}" class="form-input" min="1">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Max Instagram Accounts Per User') }}</label>
                            <input type="number" name="max_instagram_accounts" value="{{ old('max_instagram_accounts', $settings['max_instagram_accounts'] ?? 5) }}" class="form-input" min="1">
                        </div>
                    </div>
                </div>

                {{-- Features --}}
                <div x-show="activeTab === 'features'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-4">{{ __('Platform Features') }}</h2>
                    <div class="max-w-2xl space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="coupons_enabled" value="1"
                                   {{ ($settings['coupons_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-500 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="font-medium text-primary text-sm">{{ __('Enable Coupons') }}</span>
                                <p class="text-xs text-muted">{{ __('Allow users to redeem gift codes.') }}</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="referrals_enabled" value="1"
                                   {{ ($settings['referrals_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-500 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="font-medium text-primary text-sm">{{ __('Enable Referral Program') }}</span>
                                <p class="text-xs text-muted">{{ __('Users can generate invite links and earn bonus points.') }}</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer mt-4">
                            <input type="checkbox" name="auto_approve_orders" value="1"
                                   {{ ($settings['auto_approve_orders'] ?? '0') === '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-500 rounded border-gray-300 focus:ring-brand-500">
                            <div>
                                <span class="font-medium text-primary text-sm">{{ __('Auto Approve Orders') }}</span>
                                <p class="text-xs text-muted">{{ __('If enabled, user orders will automatically become active instead of pending.') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- SMTP Configuration --}}
                <div x-show="activeTab === 'smtp'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('SMTP Configuration') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('These settings control outbound emails (like password resets). Overrides the environment variables dynamically.') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
                        <div>
                            <label class="form-label">{{ __('Mail Host') }}</label>
                            <input type="text" name="smtp_host" value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}" class="form-input" placeholder="smtp.mailgun.org">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Mail Port') }}</label>
                            <input type="number" name="smtp_port" value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}" class="form-input" placeholder="587">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Mail Username') }}</label>
                            <input type="text" name="smtp_username" value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}" class="form-input" autocomplete="off">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Mail Password') }}</label>
                            <input type="password" name="smtp_password" value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}" class="form-input" autocomplete="new-password">
                        </div>
                        <div>
                            <label class="form-label">{{ __('Encryption') }}</label>
                            <select name="smtp_encryption" class="form-input">
                                <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="" {{ ($settings['smtp_encryption'] ?? '') === '' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">{{ __('From Address') }}</label>
                            <input type="email" name="smtp_from_address" value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? 'noreply@ffinsta.com') }}" class="form-input" placeholder="noreply@ffinsta.com">
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">{{ __('From Name') }}</label>
                            <input type="text" name="smtp_from_name" value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? 'FFInsta') }}" class="form-input" placeholder="FFInsta">
                        </div>
                    </div>
                </div>

                {{-- Legal --}}
                <div x-show="activeTab === 'legal'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('Legal Policies') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('Manage your platform\'s legal documentation.') }}</p>
                    <div class="space-y-6">
                        <div>
                            <label class="form-label">{{ __('Privacy Policy') }}</label>
                            <textarea name="privacy_policy" rows="8" class="form-input font-mono text-sm" placeholder="Enter privacy policy (HTML allowed)">{{ old('privacy_policy', $settings['privacy_policy'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">{{ __('Terms and Conditions') }}</label>
                            <textarea name="terms_conditions" rows="8" class="form-input font-mono text-sm" placeholder="Enter terms & conditions (HTML allowed)">{{ old('terms_conditions', $settings['terms_conditions'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">{{ __('Refund Policy') }}</label>
                            <textarea name="refund_policy" rows="8" class="form-input font-mono text-sm" placeholder="Enter refund policy (HTML allowed)">{{ old('refund_policy', $settings['refund_policy'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Instagram API --}}
                <div x-show="activeTab === 'instagram'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('Instagram API Settings') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('Configure how users connect their Instagram accounts.') }}</p>
                    
                    <div class="max-w-2xl space-y-6">
                        <div>
                            <label class="form-label">{{ __('Authentication Method') }}</label>
                            <select name="instagram_auth_method" class="form-input">
                                <option value="simple" {{ (old('instagram_auth_method', $settings['instagram_auth_method'] ?? 'simple') === 'simple') ? 'selected' : '' }}>{{ __('Simple (Username Entry)') }}</option>
                                <option value="oauth" {{ (old('instagram_auth_method', $settings['instagram_auth_method'] ?? 'simple') === 'oauth') ? 'selected' : '' }}>{{ __('OAuth (Login with Instagram)') }}</option>
                            </select>
                            <p class="text-xs text-muted mt-1">{{ __('OAuth requires a verified Meta App Client ID and Secret.') }}</p>
                        </div>

                        <div>
                            <label class="form-label">{{ __('Instagram Client ID') }}</label>
                            <input type="text" name="instagram_client_id" value="{{ old('instagram_client_id', $settings['instagram_client_id'] ?? '') }}" class="form-input" placeholder="e.g. 123456789012345">
                        </div>

                        <div>
                            <label class="form-label">{{ __('Instagram Client Secret') }}</label>
                            <input type="password" name="instagram_client_secret" value="{{ old('instagram_client_secret', $settings['instagram_client_secret'] ?? '') }}" class="form-input" placeholder="e.g. 8a8b8c...">
                        </div>
                    </div>
                </div>

                {{-- Payments Configuration --}}
                <div x-show="activeTab === 'payments'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('Payments Settings') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('Configure the SMS Payment Gateway and Binance Pay settings.') }}</p>
                    
                    <div class="max-w-2xl space-y-6">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-850">
                            <label class="form-label font-bold text-sm mb-1">{{ __('Points Conversion Rate') }}</label>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-muted">1 USD =</span>
                                <input type="number" name="points_per_usd" value="{{ old('points_per_usd', $settings['points_per_usd'] ?? 200) }}" class="form-input text-center font-mono w-32" min="1" required>
                                <span class="text-sm text-primary font-semibold">{{ __('Points') }}</span>
                            </div>
                            <p class="text-xs text-muted mt-1.5">{{ __('Controls how many points the user receives for every $1 USD spent.') }}</p>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <h3 class="font-bold text-primary text-sm">{{ __('Vodafone Cash (SMS Payment Gateway)') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">{{ __('Store ID') }}</label>
                                    <input type="text" name="sms_payment_store_id" value="{{ old('sms_payment_store_id', $settings['sms_payment_store_id'] ?? '') }}" class="form-input font-mono" placeholder="e.g. 101">
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Store Secret Key') }}</label>
                                    <input type="password" name="sms_payment_store_key" value="{{ old('sms_payment_store_key', $settings['sms_payment_store_key'] ?? '') }}" class="form-input" placeholder="e.g. secret_key_here">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <h3 class="font-bold text-primary text-sm">{{ __('Binance Pay Settings') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">{{ __('Binance Pay ID') }}</label>
                                    <input type="text" name="binance_pay_id" value="{{ old('binance_pay_id', $settings['binance_pay_id'] ?? '') }}" class="form-input font-mono" placeholder="e.g. 252848392">
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Binance QR Code Image') }}</label>
                                    <input type="file" name="binance_qr_code" class="form-input text-xs" accept="image/*">
                                </div>
                                @if($settings['binance_qr_code'] ?? null)
                                    <div class="md:col-span-2">
                                        <span class="text-xs text-muted block mb-1.5">{{ __('Current QR Code Preview:') }}</span>
                                        <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-2xl inline-block border border-slate-200 dark:border-slate-700">
                                            <img src="{{ $settings['binance_qr_code'] }}" alt="Binance QR Code" class="w-32 h-32 object-contain">
                                        </div>
                                    </div>
                                @endif
                                <div>
                                    <label class="form-label">{{ __('Binance Read-Only API Key') }}</label>
                                    <input type="text" name="binance_api_key" value="{{ old('binance_api_key', $settings['binance_api_key'] ?? '') }}" class="form-input font-mono" placeholder="e.g. read_only_api_key">
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Binance Read-Only API Secret') }}</label>
                                    <input type="password" name="binance_api_secret" value="{{ old('binance_api_secret', $settings['binance_api_secret'] ?? '') }}" class="form-input" placeholder="e.g. api_secret_here">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RapidAPI Settings --}}
                <div x-show="activeTab === 'rapidapi'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('RapidAPI Settings') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('Configure dynamic RapidAPI keys and hosts for Instagram follow checks.') }}</p>
                    
                    <div class="max-w-2xl space-y-4">
                        <div>
                            <label class="form-label">{{ __('RapidAPI Key') }}</label>
                            <input type="password" name="rapidapi_key" value="{{ old('rapidapi_key', $settings['rapidapi_key'] ?? '') }}" class="form-input font-mono" placeholder="e.g. 89ef3a5fb4msha42105dec8ebbedp1c1247jsn4eeb51f8129e">
                        </div>
                        <div>
                            <label class="form-label">{{ __('RapidAPI Host') }}</label>
                            <input type="text" name="rapidapi_host" value="{{ old('rapidapi_host', $settings['rapidapi_host'] ?? 'instagram-looter2.p.rapidapi.com') }}" class="form-input font-mono" placeholder="instagram-looter2.p.rapidapi.com">
                            <p class="text-xs text-muted mt-1">{{ __('Example: instagram-looter2.p.rapidapi.com') }}</p>
                        </div>
                    </div>
                </div>

                {{-- SEO Settings --}}
                <div x-show="activeTab === 'seo'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('Search Engine Optimization (SEO)') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('Manage global meta tags to improve your site visibility on Google and other search engines.') }}</p>
                    
                    <div class="max-w-3xl space-y-5">
                        <div>
                            <label class="form-label">{{ __('SEO Meta Title') }}</label>
                            <input type="text" name="seo_title" value="{{ old('seo_title', $settings['seo_title'] ?? config('app.name')) }}" class="form-input" placeholder="e.g. FFInsta - Grow Your Instagram Followers">
                            <p class="text-xs text-muted mt-1">{{ __('The main title tag used on the homepage and as a fallback. Best under 60 characters.') }}</p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('SEO Meta Description') }}</label>
                            <textarea name="seo_description" rows="3" class="form-input" placeholder="e.g. Earn followers the smart way. Place orders, complete tasks, and grow your Instagram presence.">{{ old('seo_description', $settings['seo_description'] ?? '') }}</textarea>
                            <p class="text-xs text-muted mt-1">{{ __('A brief description of your platform. Best under 160 characters.') }}</p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('SEO Keywords') }}</label>
                            <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $settings['seo_keywords'] ?? '') }}" class="form-input" placeholder="e.g. instagram followers, earn followers, social media growth">
                            <p class="text-xs text-muted mt-1">{{ __('Comma-separated list of keywords.') }}</p>
                        </div>
                        <div>
                            <label class="form-label">{{ __('Open Graph (OG) Image') }}</label>
                            <input type="file" name="seo_og_image" class="form-input" accept="image/*">
                            @if(config('settings.seo_og_image'))
                                <div class="mt-2 p-2 bg-slate-100 dark:bg-slate-800 rounded-xl inline-block border border-slate-200 dark:border-slate-700">
                                    <img src="{{ config('settings.seo_og_image') }}" alt="OG Image" class="h-16 object-contain">
                                </div>
                            @endif
                            <p class="text-xs text-muted mt-1">{{ __('The image shown when your site is shared on Facebook, Twitter, WhatsApp, etc. (Recommended: 1200x630px)') }}</p>
                        </div>
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                            <label class="form-label">{{ __('Custom Head Code (Advanced)') }}</label>
                            <textarea name="seo_custom_head" rows="4" class="form-input font-mono text-xs" placeholder="<meta name='robots' content='index, follow'>&#10;<!-- Any other custom meta tags -->">{{ old('seo_custom_head', $settings['seo_custom_head'] ?? '') }}</textarea>
                            <p class="text-xs text-muted mt-1">{{ __('Injected directly into the <head> section. Use for verification tags, custom schema, or additional meta properties.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- GEO Settings --}}
                <div x-show="activeTab === 'geo'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <h2 class="text-lg font-bold mb-2">{{ __('Generative Engine Optimization (GEO)') }}</h2>
                    <p class="text-sm text-muted mb-5">{{ __('Optimize your site for AI-driven search engines (like ChatGPT, Perplexity, and Google AI Overviews).') }}</p>
                    
                    <div class="max-w-3xl space-y-5">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>Tip:</strong> Generative AI models look for clear factual statements, structured data, and authority. Use these fields to inject structured context invisible to normal users but highly readable by AI bots.
                            </p>
                        </div>
                        
                        <div>
                            <label class="form-label">{{ __('Platform Definition (AI Context)') }}</label>
                            <textarea name="geo_platform_definition" rows="4" class="form-input" placeholder="FFInsta is a leading points-based exchange platform where users can organically grow their Instagram followers by completing mutually beneficial tasks...">{{ old('geo_platform_definition', $settings['geo_platform_definition'] ?? '') }}</textarea>
                            <p class="text-xs text-muted mt-1">{{ __('A clear, factual, encyclopedic definition of what your platform does. This helps AI models accurately summarize your service.') }}</p>
                        </div>
                        
                        <div>
                            <label class="form-label">{{ __('Key Features / Value Proposition (Structured)') }}</label>
                            <textarea name="geo_key_features" rows="4" class="form-input font-mono text-sm" placeholder="1. Free organic Instagram growth&#10;2. Secure API connection&#10;3. Real human followers">{{ old('geo_key_features', $settings['geo_key_features'] ?? '') }}</textarea>
                            <p class="text-xs text-muted mt-1">{{ __('List the top factual benefits. This is often parsed into bullet points by AI engines.') }}</p>
                        </div>

                        <div>
                            <label class="form-label">{{ __('Target Audience / Use Cases') }}</label>
                            <input type="text" name="geo_target_audience" value="{{ old('geo_target_audience', $settings['geo_target_audience'] ?? '') }}" class="form-input" placeholder="e.g. Influencers, Small Businesses, Content Creators">
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                            <label class="form-label">{{ __('Custom Schema.org JSON-LD') }}</label>
                            <textarea name="geo_schema_json" rows="6" class="form-input font-mono text-xs" placeholder="{&#10;  &quot;@@context&quot;: &quot;https://schema.org&quot;,&#10;  &quot;@@type&quot;: &quot;SoftwareApplication&quot;,&#10;  &quot;name&quot;: &quot;FFInsta&quot;&#10;}">{{ old('geo_schema_json', $settings['geo_schema_json'] ?? '') }}</textarea>
                            <p class="text-xs text-muted mt-1">{{ __('Structured data is crucial for GEO. Enter valid JSON-LD. It will be automatically wrapped in <script type="application/ld+json">.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary px-8">{{ __('Save Settings') }}</button>
            </div>
        </form>
    </div>
</x-admin-layout>
