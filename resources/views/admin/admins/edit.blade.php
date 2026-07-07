<x-admin-layout>
    <x-slot name="title">{{ __('Edit Admin') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.admins.index') }}" class="text-muted hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rtl:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            {{ __('Edit Admin') }}: {{ $admin->name }}
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card p-6">
            <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" 
                           class="form-input" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" 
                           class="form-input" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5">
                    <h3 class="text-sm font-semibold mb-3">{{ __('Permissions') }}</h3>
                    
                    @if($admin->id === 1)
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded text-sm mb-3">
                            {{ __('This is the main administrator account. It automatically has all permissions.') }}
                        </div>
                    @endif

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
                            $currentPerms = is_array(old('permissions')) ? old('permissions') : ($admin->permissions ?? []);
                        @endphp
                        
                        @foreach($availablePermissions as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer {{ $admin->id === 1 ? 'opacity-60 cursor-not-allowed' : '' }}">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                    class="form-checkbox text-primary rounded"
                                    {{ in_array($key, $currentPerms) || $admin->id === 1 ? 'checked' : '' }}
                                    {{ $admin->id === 1 ? 'disabled' : '' }}>
                                <span class="text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5">
                    <h3 class="text-sm font-semibold mb-3">{{ __('Change Password') }} <span class="text-xs text-muted font-normal">({{ __('Leave blank to keep current password') }})</span></h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input type="password" name="password" id="password" class="form-input">
                            @error('password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-5">
                    <button type="submit" class="btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
