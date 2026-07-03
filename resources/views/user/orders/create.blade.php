<x-app-layout>
    <x-slot name="title">{{ __('Create Order') }}</x-slot>
    <x-slot name="header">{{ __('Create Order') }}</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card p-6">
            {{-- Points info banner --}}
            <div class="rounded-xl bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-800 p-4 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-800 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-muted">{{ __('Your Balance') }}</p>
                    <p class="text-xl font-bold text-brand-600 dark:text-brand-400">
                        {{ number_format($userPoints) }} <span class="text-sm font-normal">pts</span>
                    </p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-xs text-muted">{{ __('Cost per follower') }}</p>
                    <p class="text-sm font-semibold text-primary">{{ $pointsPerFollow }} pts</p>
                </div>
            </div>

            <form method="POST" action="{{ route('user.orders.store') }}" id="order-form"
                  x-data="{ qty: 10, perFollow: {{ $pointsPerFollow }}, balance: {{ $userPoints }}, isSubmitting: false }"
                  @submit.prevent="if(qty * perFollow <= balance && !isSubmitting) { isSubmitting = true; $el.submit(); }">
                @csrf

                {{-- Instagram Account --}}
                <div class="mb-5">
                    <label class="form-label" for="instagram_account_id">{{ __('Instagram Account') }}</label>
                    @if($accounts->isEmpty())
                        <div class="alert-warning text-sm">
                            {{ __('You have no active Instagram accounts.') }}
                            <a href="{{ route('user.instagram.create') }}" class="font-semibold underline ml-1">{{ __('Add one now') }}</a>
                        </div>
                    @else
                        <select name="instagram_account_id" id="instagram_account_id" class="form-input" required>
                            <option value="">{{ __('Select account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('instagram_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ '@' . $account->username }}
                                    @if($account->is_default) ★ @endif
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('instagram_account_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Quantity --}}
                <div class="mb-5">
                    <label class="form-label" for="quantity">{{ __('Number of Followers') }}</label>
                    <input type="number" name="quantity" id="quantity" x-model.number="qty"
                           class="form-input" min="1" max="10000"
                           value="{{ old('quantity', 10) }}" required>
                    @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Cost summary --}}
                <div class="rounded-xl p-4 mb-6" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-muted">{{ __('Followers requested') }}</span>
                        <span x-text="qty.toLocaleString()" class="font-semibold text-primary"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-muted">{{ __('Cost per follower') }}</span>
                        <span class="font-semibold text-primary">{{ $pointsPerFollow }} pts</span>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="flex justify-between font-bold">
                        <span class="text-primary">{{ __('Total Cost') }}</span>
                        <span :class="qty * perFollow > balance ? 'text-red-500' : 'text-brand-500'"
                              x-text="(qty * perFollow).toLocaleString() + ' pts'"></span>
                    </div>
                    <div x-show="qty * perFollow > balance" class="mt-3 text-xs bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800/30 p-3 rounded-lg flex items-center justify-between">
                        <span class="text-red-600 dark:text-red-400 font-medium">{{ __('Insufficient points.') }}</span>
                        <a href="{{ route('user.purchase-points.index') }}" class="btn-primary py-1.5 px-3 inline-flex text-xs items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Buy Points') }}
                        </a>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full"
                        :disabled="qty * perFollow > balance || qty < 1 || isSubmitting"
                        :class="(qty * perFollow > balance || isSubmitting) ? 'opacity-50 cursor-not-allowed' : ''">
                    <span x-show="!isSubmitting">{{ __('Place Order') }}</span>
                    <span x-show="isSubmitting" x-cloak>{{ __('Processing...') }}</span>
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
