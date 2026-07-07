<x-admin-layout>
    <x-slot name="title">{{ __('Add Admin') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.admins.index') }}" class="text-muted hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rtl:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            {{ __('Add Admin') }}
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card p-6">
            <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="form-input" required autofocus>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="form-input" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input type="password" name="password" id="password" 
                           class="form-input" required>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="form-input" required>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5">
                    <h3 class="text-sm font-semibold mb-3">{{ __('Permissions') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $availablePermissions = [
                                'users'     => __('Users Management'),
                                'admins'    => __('Admins Management'),
                                'orders'    => __('Orders Management'),
                                'instagram' => __('Instagram Accounts'),
                                'coupons'   => __('Coupons'),
                                'payments'  => __('Payments Logs'),
                                'posts'     => __('Blog Posts'),
                                'faqs'      => __('FAQs'),
                                'logs'      => __('System Logs'),
                                'settings'  => __('System Settings'),
                            ];
                        @endphp
                        
                        @foreach($availablePermissions as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                    class="form-checkbox text-primary rounded"
                                    {{ is_array(old('permissions')) && in_array($key, old('permissions')) ? 'checked' : '' }}>
                                <span class="text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-5">
                    <button type="submit" class="btn-primary">{{ __('Create Admin') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
