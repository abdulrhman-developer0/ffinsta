<x-admin-layout>
    <x-slot name="title">{{ __('Create Manual Order') }}</x-slot>
    <x-slot name="header">{{ __('Create Manual Order') }}</x-slot>

    <div class="max-w-lg mx-auto">
        <div class="card p-6">
            @error('order')
                <div class="alert-error mb-4">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-4"
                  x-data="orderForm()"
                  @submit.prevent="submitForm">
                @csrf

                <div>
                    <label class="form-label">{{ __('User') }}</label>
                    <select name="user_id" x-model="selectedUser" class="form-input" required>
                        <option value="">{{ __('Select user…') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Instagram Account') }}</label>
                    
                    <div class="flex items-center gap-4 mb-2">
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" x-model="mode" value="existing">
                            <span>{{ __('Select Existing') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" x-model="mode" value="custom">
                            <span>{{ __('Enter Custom Username') }}</span>
                        </label>
                    </div>

                    <div x-show="mode === 'existing'">
                        <select name="instagram_account_id" class="form-input" :required="mode === 'existing'">
                            <option value="">{{ __('Select account…') }}</option>
                            <template x-for="account in accounts" :key="account.id">
                                <option :value="account.id" x-text="'@' + account.username" :selected="account.id == '{{ old('instagram_account_id') }}'"></option>
                            </template>
                        </select>
                        <p x-show="selectedUser && accounts.length === 0" class="text-sm text-red-500 mt-1" x-cloak>{{ __('This user has no connected Instagram accounts.') }}</p>
                        @error('instagram_account_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="mode === 'custom'" x-cloak class="space-y-3">
                        <div class="flex items-center">
                            <span class="px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 rounded-l-md font-medium">@</span>
                            <input type="text" x-model="customUsername" name="custom_username" class="form-input rounded-l-none" placeholder="username" :required="mode === 'custom'">
                        </div>
                        @error('custom_username') <p class="form-error">{{ $message }}</p> @enderror

                        <!-- Profile Picture Picker -->
                        <div>
                            <input type="hidden" name="profile_picture_url" :value="selectedImage">
                            <div class="flex items-center gap-3">
                                <template x-if="selectedImage">
                                    <img :src="'{{ route('proxy.image') }}?url=' + encodeURIComponent(selectedImage)" class="w-10 h-10 rounded-full object-cover border-2 border-brand-500">
                                </template>
                                <button type="button" @click="searchImages()" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1" :disabled="isSearching || !customUsername">
                                    <svg x-show="isSearching" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="isSearching ? '{{ __('Fetching...') }}' : (selectedImage ? '{{ __('Fetch Again') }}' : '{{ __('Fetch Profile Picture') }}')"></span>
                                </button>
                            </div>
                            <p x-show="error" x-text="error" class="text-xs text-red-500 mt-1" style="display:none;"></p>

                            <!-- Results Modal -->
                        </div>
                    </div>

                <div>
                    <label class="form-label">{{ __('Followers Requested') }}</label>
                    <input type="number" name="requested_qty" value="{{ old('requested_qty', 100) }}"
                           class="form-input" min="10" max="10000" step="10" required>
                    @error('requested_qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Priority') }}</label>
                    <select name="priority" class="form-input">
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                        <option value="high"   {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ __('Deduct Points from User?') }}</label>
                    <div class="flex items-center gap-3 mt-1">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="deduct_points" value="1" {{ old('deduct_points', '0') === '1' ? 'checked' : '' }}>
                            {{ __('Yes — deduct normal cost') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="deduct_points" value="0" {{ old('deduct_points', '0') === '0' ? 'checked' : '' }} checked>
                            {{ __('No — free manual order') }}
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label">{{ __('Admin Notes') }} <span class="text-muted text-xs">({{ __('optional') }})</span></label>
                    <textarea name="admin_notes" class="form-input" rows="3"
                              placeholder="{{ __('Internal notes about this order…') }}">{{ old('admin_notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.orders.index') }}" class="btn-secondary flex-1">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-primary flex-1"
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''">
                        <span x-show="!isSubmitting">{{ __('Create Order') }}</span>
                        <span x-show="isSubmitting" x-cloak>{{ __('Processing...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderForm', () => ({
                isSubmitting: false,
                users: {{ \Illuminate\Support\Js::from($users->map->only('id')->map(function($user) use ($users) {
                    return ['id' => $user['id'], 'accounts' => $users->find($user['id'])->instagramAccounts->map->only('id', 'username')];
                })) }},
                selectedUser: '{{ old('user_id') }}',
                mode: '{{ old('custom_username') ? 'custom' : 'existing' }}',
                customUsername: '{{ old('custom_username') }}',
                selectedImage: '{{ old('profile_picture_url') }}',
                isSearching: false,
                error: null,
                
                get accounts() {
                    if (!this.selectedUser) return [];
                    const user = this.users.find(u => u.id == this.selectedUser);
                    return user ? user.accounts : [];
                },
                
                async searchImages() {
                    if(!this.customUsername) return false;
                    this.isSearching = true;
                    this.error = null;
                    try {
                        const res = await fetch('{{ route('search.images') }}?username=' + encodeURIComponent(this.customUsername));
                        const data = await res.json();
                        if(data.success && data.image) {
                            this.selectedImage = data.image;
                            return true;
                        } else {
                            this.error = data.message || '{{ __('No images found.') }}';
                            return false;
                        }
                    } catch(e) {
                        this.error = '{{ __('Network error.') }}';
                        return false;
                    } finally {
                        this.isSearching = false;
                    }
                },
                
                async submitForm(event) {
                    if (this.isSubmitting) return;
                    
                    if (this.mode === 'custom' && this.customUsername && !this.selectedImage) {
                        this.isSubmitting = true;
                        await this.searchImages();
                        // Wait for Alpine to update the hidden input with the new image URL
                        await this.$nextTick();
                    }
                    
                    this.isSubmitting = true;
                    this.$el.submit();
                }
            }));
        });
    </script>
</x-admin-layout>
