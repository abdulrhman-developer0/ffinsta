<x-app-layout>
    <x-slot name="title">{{ __('My Instagram Accounts') }}</x-slot>
    <x-slot name="header">{{ __('Instagram Accounts') }}</x-slot>

    <div class="flex justify-end mb-5">
        <a href="{{ route('user.instagram.create') }}" class="btn-primary btn-sm px-5">
            + {{ __('Add Account') }}
        </a>
    </div>

    @error('username')
        <div class="alert-error mb-4 animate-slide-up" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    @error('oauth')
        <div class="alert-error mb-4 animate-slide-up" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($accounts as $account)
            <div class="card p-5 relative">
                @if($account->is_default)
                    <span class="absolute top-3 right-3 badge badge-completed text-[10px]">{{ __('Default') }}</span>
                @endif

                <div class="flex items-center gap-3 mb-4">
                    @if($account->profile_picture_url)
                        <img src="{{ route('proxy.image', ['url' => $account->profile_picture_url]) }}" alt="{{ $account->username }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 shadow-sm border border-gray-200 dark:border-gray-700">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($account->username, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-primary">{{ '@' . $account->username }}</p>
                        <span class="badge-{{ $account->status }} text-[10px]">{{ ucfirst($account->status) }}</span>
                    </div>
                </div>

                <p class="text-xs text-muted mb-3">{{ __('Added') }}: {{ $account->created_at->format('M d, Y') }}</p>

                <div class="flex flex-wrap gap-2">
                    @if(!$account->is_default)
                        <form method="POST" action="{{ route('user.instagram.set-default', $account) }}">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">{{ __('Set Default') }}</button>
                        </form>
                    @endif
                    <a href="{{ route('user.instagram.edit', $account) }}" class="btn-secondary btn-sm">{{ __('Edit') }}</a>
                    <form method="POST" action="{{ route('user.instagram.destroy', $account) }}"
                          onsubmit="return confirm('{{ __('Remove this account?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm">{{ __('Remove') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 card p-10 text-center text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-3 opacity-30" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                </svg>
                <p class="font-medium">{{ __('No Instagram accounts connected') }}</p>
                <a href="{{ route('user.instagram.create') }}" class="btn-primary btn-sm mt-4 inline-flex">{{ __('Add Your First Account') }}</a>
            </div>
        @endforelse
    </div>
</x-app-layout>
