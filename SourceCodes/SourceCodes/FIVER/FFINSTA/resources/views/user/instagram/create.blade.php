<x-app-layout>
    <x-slot name="title">{{ __('Add Instagram Account') }}</x-slot>
    <x-slot name="header">{{ __('Add Instagram Account') }}</x-slot>

    <div class="max-w-md mx-auto">
        <div class="card p-6">
            @error('oauth')
                <div class="alert alert-error mb-4">
                    {{ $message }}
                </div>
            @enderror

            @php
                $authMethod = app(\App\Services\SettingService::class)->get('instagram_auth_method', 'simple');
            @endphp

            @if($authMethod === 'oauth')
                <!-- OAuth Method -->
                <div class="mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-center text-primary font-bold mb-1">{{ __('Connect with Instagram') }}</p>
                    <p class="text-xs text-muted text-center">{{ __('Securely connect your Instagram account to start earning points.') }}</p>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <a href="{{ route('user.instagram.oauth.redirect') }}" class="btn-primary w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 border-0 shadow-glow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324z"/>
                        </svg>
                        {{ __('Login with Instagram') }}
                    </a>
                    <a href="{{ route('user.instagram.index') }}" class="btn-secondary w-full text-center">{{ __('Cancel') }}</a>
                </div>
            @else
                <!-- Simple Method -->
                <!-- Simple Method -->
                <div x-data="instagramVerification()">
                    <div x-show="step === 1" x-transition>
                        <div class="mb-5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-muted text-center">{{ __('Enter your Instagram username to connect your account.') }}</p>
                        </div>

                        <form @submit.prevent="verifyProfile" class="space-y-4">
                            <div>
                                <label class="form-label" for="username">{{ __('Instagram Username') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted text-sm">@</span>
                                    <input type="text" id="username" name="username" x-model="username"
                                           class="form-input pl-7"
                                           placeholder="yourhandle"
                                           maxlength="30" required
                                           pattern="[a-zA-Z0-9._]{1,30}"
                                           :disabled="loading">
                                </div>
                                <p x-show="error" x-text="error" class="form-error mt-1" style="display: none;"></p>
                                @error('username') <p class="form-error mt-1">{{ $message }}</p> @enderror
                                <p class="text-xs text-muted mt-1">{{ __('Only letters, numbers, dots, and underscores.') }}</p>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <a href="{{ route('user.instagram.index') }}" class="btn-secondary flex-1" :class="{'opacity-50 pointer-events-none': loading}">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn-primary flex-1 flex justify-center items-center gap-2" :disabled="loading">
                                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? '{{ __('Verifying...') }}' : '{{ __('Verify Account') }}'"></span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div x-show="step === 2" x-transition style="display: none;">
                        <div class="mb-5 text-center">
                            <h3 class="text-lg font-bold mb-1">{{ __('Is this you?') }}</h3>
                            <p class="text-sm text-muted">{{ __('Please verify your profile details.') }}</p>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6 border border-gray-100 dark:border-gray-700 text-center">
                            <img :src="'{{ route('proxy.image') }}?url=' + encodeURIComponent(profileData.pic)" alt="Profile" class="w-20 h-20 rounded-full mx-auto mb-3 object-cover shadow-sm border-2 border-white dark:border-gray-700">
                            <h4 class="font-bold text-lg mb-1" x-text="'@' + username"></h4>
                            <p class="text-sm text-muted" x-text="profileData.stats"></p>
                        </div>

                        <form method="POST" action="{{ route('user.instagram.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="username" :value="username">
                            <input type="hidden" name="profile_picture_url" :value="profileData.pic">
                            
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="step = 1; error = null;" class="btn-secondary flex-1">{{ __('No, Go Back') }}</button>
                                <button type="submit" class="btn-primary flex-1">{{ __('Yes, Add Account') }}</button>
                            </div>
                        </form>
                    </div>

                    <!-- Force Add fallback -->
                    <div x-show="allowForceAdd" x-transition class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700" style="display: none;">
                        <p class="text-xs text-muted mb-2 text-center">{{ __('Is your account private or having trouble verifying? You can add it manually.') }}</p>
                        <form method="POST" action="{{ route('user.instagram.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="username" :value="username">
                            
                            <!-- Profile Picture Picker -->
                            <div x-data="imagePicker()" class="mb-4">
                                <label class="form-label text-xs">{{ __('Profile Picture (Optional)') }}</label>
                                <input type="hidden" name="profile_picture_url" :value="selectedImage">
                                <div class="flex items-center gap-3">
                                    <template x-if="selectedImage">
                                        <img :src="'{{ route('proxy.image') }}?url=' + encodeURIComponent(selectedImage)" class="w-10 h-10 rounded-full object-cover border-2 border-brand-500">
                                    </template>
                                    <button type="button" @click="searchImages()" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1" :disabled="isSearching || !username">
                                        <svg x-show="isSearching" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span x-text="isSearching ? '{{ __('Fetching...') }}' : (selectedImage ? '{{ __('Fetch Again') }}' : '{{ __('Fetch Profile Picture') }}')"></span>
                                    </button>
                                </div>
                                <p x-show="error" x-text="error" class="text-xs text-red-500 mt-1" style="display:none;"></p>

                                <!-- Results Modal -->
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn-primary w-full">{{ __('Force Add Account') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('instagramVerification', () => ({
                            step: 1,
                            username: '{{ old('username') }}',
                            loading: false,
                            error: null,
                            profileData: { pic: '', stats: '' },
                            allowForceAdd: false,

                            async verifyProfile() {
                                if(!this.username) return;
                                
                                this.loading = true;
                                this.error = null;
                                this.allowForceAdd = false;
                                
                                try {
                                    const response = await fetch('{{ route('user.instagram.verify') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ username: this.username })
                                    });
                                    
                                    const data = await response.json();
                                    
                                    if(response.ok) {
                                        this.profileData.pic = data.profile_pic_url;
                                        this.profileData.stats = data.stats_string;
                                        this.step = 2;
                                    } else {
                                        this.error = data.error || '{{ __('Verification failed.') }}';
                                        if (data.allow_force_add) {
                                            this.allowForceAdd = true;
                                        }
                                    }
                                } catch (e) {
                                    this.error = '{{ __('Network error. Please try again.') }}';
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }));

                        Alpine.data('imagePicker', () => ({
                            isSearching: false,
                            selectedImage: '',
                            error: null,
                            
                            async searchImages() {
                                // here, username is bound to the parent instagramVerification component's username via x-data scope
                                // wait, we are in a nested x-data? Alpine handles nested data by traversing up, but `username` is in the parent.
                                // We can access it if `this.username` works, or we pass it. But let's just grab the input value to be safe.
                                const usernameVal = document.getElementById('username').value;
                                if(!usernameVal) return;
                                
                                this.isSearching = true;
                                this.error = null;
                                try {
                                    const res = await fetch('{{ route('search.images') }}?username=' + encodeURIComponent(usernameVal));
                                    const data = await res.json();
                                    if(data.success && data.image) {
                                        this.selectedImage = data.image;
                                    } else {
                                        this.error = data.message || '{{ __('No images found.') }}';
                                    }
                                } catch(e) {
                                    this.error = '{{ __('Network error.') }}';
                                } finally {
                                    this.isSearching = false;
                                }
                            }
                        }));
                    });
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
