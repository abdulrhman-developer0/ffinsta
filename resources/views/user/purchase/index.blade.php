<x-app-layout>
    <x-slot name="title">{{ __('Buy Points') }}</x-slot>
    <x-slot name="header">{{ __('Buy Points') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6" 
         x-data="{ 
             usdAmount: 5,
             pointsPerUsd: {{ $pointsPerUsd }},
             exchangeRate: {{ $rate }},
             selectedMethod: 'vodafone_cash',
             showVodafoneModal: false,
             showBinanceModal: false,
             senderPhone: '',
             binanceOrderId: '',
             isSubmittingVodafone: false,
             isSubmittingBinance: false,

             get pointsResult() {
                 return Math.floor(this.usdAmount * this.pointsPerUsd);
             },
             get egpResult() {
                 return (this.usdAmount * this.exchangeRate).toFixed(2);
             },
             copyToClipboard(text, message) {
                 navigator.clipboard.writeText(text);
                 alert(message);
             },
             submitPaymentForm(event, method) {
                 if (method === 'vodafone') this.isSubmittingVodafone = true;
                 if (method === 'binance') this.isSubmittingBinance = true;

                 let formData = new FormData(event.target);
                 fetch(event.target.action, {
                     method: 'POST',
                     body: formData,
                     headers: {
                         'X-Requested-With': 'XMLHttpRequest',
                         'Accept': 'application/json'
                     }
                 })
                 .then(response => response.json())
                 .then(data => {
                     if (method === 'vodafone') this.isSubmittingVodafone = false;
                     if (method === 'binance') this.isSubmittingBinance = false;

                     if (data.success) {
                         Alpine.store('toast').success(data.message);
                         if (data.redirect) {
                             setTimeout(() => window.location.href = data.redirect, 2000);
                         } else {
                             setTimeout(() => window.location.reload(), 2000);
                         }
                     } else {
                         let msg = data.message || 'An error occurred.';
                         if (data.errors) {
                             msg = Object.values(data.errors).flat().join(' ');
                         }
                         Alpine.store('toast').error(msg);
                     }
                 })
                 .catch(error => {
                     if (method === 'vodafone') this.isSubmittingVodafone = false;
                     if (method === 'binance') this.isSubmittingBinance = false;
                     Alpine.store('toast').error('An error occurred during verification. Please try again.');
                 });
             }
         }">

        {{-- Configuration Error --}}
        @if(!$storeId && !$binancePayId)
            <div class="card p-8 text-center space-y-4 max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-primary">{{ __('Payment Gateway Offline') }}</h3>
                <p class="text-sm text-muted max-w-sm mx-auto">
                    {{ __('Points purchase options are currently undergoing maintenance. Please check back later.') }}
                </p>
            </div>
        @else
            {{-- Unified Custom Points Calculator Card --}}
            <div class="card p-6 bg-gradient-to-br from-surface to-surface border border-slate-200 dark:border-slate-800 shadow-xl rounded-3xl relative overflow-hidden">
                <div class="absolute -right-24 -top-24 w-52 h-52 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <h2 class="text-xl font-extrabold text-primary mb-6">{{ __('Calculate Your Points') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    {{-- Left side: Amount input & presets --}}
                    <div class="space-y-5">
                        <div>
                            <label class="form-label font-bold text-xs text-primary mb-2 block">{{ __('Enter Amount in USD ($)') }}</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-muted">$</span>
                                <input type="number" 
                                       x-model.number="usdAmount" 
                                       min="1" 
                                       step="1"
                                       class="form-input text-lg font-bold text-primary ps-8 pe-4 py-3 bg-surface-2 border-slate-200 dark:border-slate-800 focus:border-brand-500 rounded-2xl w-full font-mono" />
                            </div>
                        </div>

                        {{-- Quick Presets --}}
                        <div class="space-y-2">
                            <span class="text-xs text-muted font-semibold block">{{ __('Quick select presets:') }}</span>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="val in [5, 10, 20, 50, 100]">
                                    <button type="button" 
                                            @click="usdAmount = val" 
                                            :class="usdAmount === val ? 'bg-brand-500 text-white shadow-glow border-brand-500' : 'bg-surface-3 hover:bg-surface-4 text-secondary border-slate-200 dark:border-slate-800'"
                                            class="px-4 py-2 text-sm font-bold rounded-xl border transition duration-150 font-mono">
                                        $<span x-text="val"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right side: Calculated rewards summary --}}
                    <div class="bg-slate-50 dark:bg-slate-800/30 rounded-3xl p-6 border border-slate-100 dark:border-slate-800/60 space-y-4 text-center md:text-start">
                        <div>
                            <span class="text-[10px] font-bold text-muted uppercase tracking-wider block">{{ __('Total Reward Points') }}</span>
                            <span class="text-3xl font-extrabold text-brand-600 dark:text-brand-400 font-mono tracking-tight">
                                <span x-text="pointsResult.toLocaleString()"></span>
                                <span class="text-sm font-normal text-muted">{{ __('pts') }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/60">
                            <span class="text-xs text-muted font-medium">{{ __('Vodafone Cash rate:') }}</span>
                            <span class="font-mono text-xs font-bold text-primary" x-text="egpResult + ' ' + '{{ __('EGP') }}'"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-muted font-medium">{{ __('Binance rate:') }}</span>
                            <span class="font-mono text-xs font-bold text-primary" x-text="'$' + usdAmount.toFixed(2) + ' ' + 'USDT'"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Method Selector & Checkout --}}
            <div class="space-y-4">
                <h3 class="section-title text-base font-bold text-primary">{{ __('Select Payment Method') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Vodafone Cash selector --}}
                    @if($storeId)
                        <div @click="selectedMethod = 'vodafone_cash'"
                             :class="selectedMethod === 'vodafone_cash' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200 dark:border-slate-800'"
                             class="card p-5 cursor-pointer flex items-start gap-4 border transition duration-150 relative overflow-hidden bg-surface hover:shadow-lg">
                            <div class="w-12 h-12 rounded-2xl bg-red-500/10 dark:bg-red-500/20 text-red-650 dark:text-red-400 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 space-y-1">
                                <h4 class="font-bold text-primary text-sm">{{ __('Vodafone Cash') }}</h4>
                                <p class="text-xs text-muted">{{ __('Egypt local cash transfer (Instant mobile wallet verification).') }}</p>
                            </div>
                            <div class="absolute top-4 right-4">
                                <input type="radio" name="pay_opt" :checked="selectedMethod === 'vodafone_cash'" class="text-brand-550 focus:ring-brand-500">
                            </div>
                        </div>
                    @endif

                    {{-- Binance Pay selector --}}
                    @if($binancePayId)
                        <div @click="selectedMethod = 'binance_pay'"
                             :class="selectedMethod === 'binance_pay' ? 'border-yellow-500 ring-2 ring-yellow-500/20' : 'border-slate-200 dark:border-slate-800'"
                             class="card p-5 cursor-pointer flex items-start gap-4 border transition duration-150 relative overflow-hidden bg-surface hover:shadow-lg">
                            <div class="w-12 h-12 rounded-2xl bg-yellow-500/10 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="flex-1 space-y-1">
                                <h4 class="font-bold text-primary text-sm">{{ __('Binance Pay') }}</h4>
                                <p class="text-xs text-muted">{{ __('Cryptocurrency payment. Transfer via Binance Pay or scan the QR code.') }}</p>
                            </div>
                            <div class="absolute top-4 right-4">
                                <input type="radio" name="pay_opt" :checked="selectedMethod === 'binance_pay'" class="text-yellow-500 focus:ring-yellow-500">
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action Button --}}
                <div class="flex justify-end pt-2">
                    <template x-if="selectedMethod === 'vodafone_cash'">
                        <button type="button" @click="showVodafoneModal = true; senderPhone = ''" class="btn-primary px-8 shadow-glow">{{ __('Purchase via Vodafone Cash') }}</button>
                    </template>
                    <template x-if="selectedMethod === 'binance_pay'">
                        <button type="button" @click="showBinanceModal = true; binanceOrderId = ''" class="btn-primary px-8 shadow-glow bg-yellow-500 hover:bg-yellow-600 border-0">{{ __('Purchase via Binance Pay') }}</button>
                    </template>
                </div>
            </div>
        @endif

        {{-- Vodafone Cash Modal Drawer --}}
        <div x-show="showVodafoneModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div x-show="showVodafoneModal" @click="showVodafoneModal = false" x-transition class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="showVodafoneModal" x-transition class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-lg p-6 relative shadow-2xl z-10 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-extrabold text-primary">{{ __('Vodafone Cash Checkout') }}</h3>
                    <button type="button" @click="showVodafoneModal = false" class="text-secondary hover:text-primary transition p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-4 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-muted font-bold uppercase tracking-wider block">{{ __('Points credit') }}</span>
                        <span class="font-bold text-primary text-sm" x-text="pointsResult.toLocaleString() + ' ' + 'pts'"></span>
                    </div>
                    <div class="text-end">
                        <span class="text-[10px] text-muted font-bold uppercase tracking-wider block">{{ __('Transfer amount') }}</span>
                        <span class="font-mono font-bold text-brand-600 dark:text-brand-400" x-text="egpResult + ' ' + 'EGP'"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-primary">{{ __('Step 1: Send money to active wallets') }}</h4>
                    <p class="text-xs text-muted">
                        {{ __('Please transfer exactly') }} <strong class="text-primary font-mono" x-text="egpResult + ' ' + 'EGP'"></strong> {{ __('to one of the following numbers:') }}
                    </p>

                    <div class="space-y-2 max-h-36 overflow-y-auto scrollbar-thin pe-1">
                        @forelse($wallets as $wallet)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl">
                                <span class="font-mono font-bold text-primary tracking-wider text-sm">{{ $wallet }}</span>
                                <button type="button" @click="copyToClipboard('{{ $wallet }}', '{{ __('Copied wallet number!') }}')" class="btn btn-secondary btn-sm flex items-center gap-1">
                                    {{ __('Copy') }}
                                </button>
                            </div>
                        @empty
                            <div class="p-3 bg-red-500/5 border border-red-500/10 text-red-500 text-xs rounded-xl text-center">
                                {{ __('No wallet numbers are configured at the moment. Please contact the administrator.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <h4 class="text-sm font-bold text-primary">{{ __('Step 2: Submit validation') }}</h4>
                    
                    <form action="/dashboard/purchase-points/initiate" method="POST" class="space-y-4" @submit.prevent="submitPaymentForm($event, 'vodafone')">
                        @csrf
                        <input type="hidden" name="usd_amount" :value="usdAmount">
                        <input type="hidden" name="payment_method" value="vodafone_cash">
                        <div>
                            <label class="form-label text-xs font-semibold text-primary mb-1">{{ __('Sending Wallet Phone Number') }}</label>
                            <input type="text" 
                                   name="sender_phone" 
                                   x-model="senderPhone"
                                   required 
                                   placeholder="e.g. 01012345678" 
                                   pattern="01[0-2,5]\d{8}"
                                   class="form-input tracking-widest text-center font-mono" />
                            <p class="text-[10px] text-muted mt-1 leading-normal">
                                {{ __('Enter the 11-digit Egyptian phone number you sent the money from.') }}
                            </p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showVodafoneModal = false" class="btn-secondary flex-1">{{ __('Cancel') }}</button>
                            <button type="submit" 
                                    :disabled="!senderPhone.match(/^01[0-2,5]\d{8}$/) || isSubmittingVodafone"
                                    class="btn-primary flex-1 shadow-glow disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmittingVodafone">{{ __('Confirm & Verify') }}</span>
                                <span x-show="isSubmittingVodafone" class="flex items-center gap-2 justify-center">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Verifying...') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Binance Pay Modal Drawer --}}
        <div x-show="showBinanceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div x-show="showBinanceModal" @click="showBinanceModal = false" x-transition class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="showBinanceModal" x-transition class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-lg p-6 relative shadow-2xl z-10 space-y-5 max-h-[90vh] overflow-y-auto scrollbar-thin">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-extrabold text-primary">{{ __('Binance Pay Checkout') }}</h3>
                    <button type="button" @click="showBinanceModal = false" class="text-secondary hover:text-primary transition p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-4 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-muted font-bold uppercase tracking-wider block">{{ __('Points credit') }}</span>
                        <span class="font-bold text-primary text-sm" x-text="pointsResult.toLocaleString() + ' ' + 'pts'"></span>
                    </div>
                    <div class="text-end">
                        <span class="text-[10px] text-muted font-bold uppercase tracking-wider block">{{ __('Transfer amount') }}</span>
                        <span class="font-mono font-bold text-yellow-600 dark:text-yellow-400" x-text="'$' + usdAmount.toFixed(2) + ' ' + 'USDT'"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-primary">{{ __('Step 1: Transfer via Binance Pay') }}</h4>
                    <p class="text-xs text-muted">
                        {{ __('Transfer exactly') }} <strong class="text-primary font-mono" x-text="'$' + usdAmount.toFixed(2) + ' ' + 'USDT'"></strong> {{ __('to the Binance Pay ID below, or scan the QR code with your mobile Binance App:') }}
                    </p>

                    @if($binancePayId)
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl">
                            <div>
                                <span class="text-[10px] text-muted font-bold uppercase block">{{ __('Binance Pay ID') }}</span>
                                <span class="font-mono font-bold text-primary tracking-wider text-sm">{{ $binancePayId }}</span>
                            </div>
                            <button type="button" @click="copyToClipboard('{{ $binancePayId }}', '{{ __('Copied Binance Pay ID!') }}')" class="btn btn-secondary btn-sm">
                                {{ __('Copy') }}
                            </button>
                        </div>
                    @endif

                    @if($binanceQrCode)
                        <div class="text-center p-3 bg-slate-50 dark:bg-slate-850 rounded-2xl border border-slate-200/50 dark:border-slate-800/50">
                            <img src="{{ $binanceQrCode }}" alt="Binance QR Code" class="w-40 h-40 object-contain mx-auto rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 bg-white p-2">
                            <span class="text-[10px] text-muted font-medium mt-2 block">{{ __('Scan this QR code with Binance Pay App to transfer funds.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <h4 class="text-sm font-bold text-primary">{{ __('Step 2: Submit validation') }}</h4>
                    
                    <form action="/dashboard/purchase-points/initiate" method="POST" class="space-y-4" @submit.prevent="submitPaymentForm($event, 'binance')">
                        @csrf
                        <input type="hidden" name="usd_amount" :value="usdAmount">
                        <input type="hidden" name="payment_method" value="binance_pay">
                        <div>
                            <label class="form-label text-xs font-semibold text-primary mb-1">{{ __('Binance Order ID') }}</label>
                            <input type="text" 
                                   name="transaction_id" 
                                   x-model="binanceOrderId"
                                   required 
                                   placeholder="{{ __('Enter Binance Order ID') }}" 
                                   class="form-input tracking-widest text-center font-mono text-sm" />
                            <p class="text-[10px] text-muted mt-1 leading-normal">
                                {{ __('Paste your Binance internal transfer Order ID / Transaction ID. It will be verified automatically.') }}
                            </p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showBinanceModal = false" class="btn-secondary flex-1">{{ __('Cancel') }}</button>
                            <button type="submit" 
                                    :disabled="binanceOrderId.trim() === '' || isSubmittingBinance"
                                    class="btn-primary flex-1 shadow-glow bg-yellow-500 hover:bg-yellow-600 border-0 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmittingBinance">{{ __('Verify Payment') }}</span>
                                <span x-show="isSubmittingBinance" class="flex items-center gap-2 justify-center">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Verifying...') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
