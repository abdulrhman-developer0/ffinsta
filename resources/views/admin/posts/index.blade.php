<x-admin-layout>
    <x-slot name="title">{{ __('Posts') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight">{{ __('Posts') }}</h2>
        </div>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, postToDelete: null, deletePostUrl: '' }">
        <!-- Add Post Button (Mobile Block) -->
        <div class="mb-6 flex justify-end">
            <a href="{{ route('admin.posts.create') }}" class="btn-primary shadow-glow px-4 py-3 sm:py-2.5 w-full sm:w-auto flex items-center justify-center gap-2 text-base sm:text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>{{ __('New Post') }}</span>
            </a>
        </div>
        
        @if($posts->isEmpty())
            <div class="card p-12 text-center text-muted border-dashed border-2 border-slate-200 dark:border-slate-700 bg-transparent shadow-none">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8" /></svg>
                </div>
                <p class="text-lg font-medium">{{ __('No posts found. Create one to get started.') }}</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach($posts as $post)
                    <div class="card p-3 sm:p-4 flex flex-row items-center gap-4 hover:shadow-md transition-all duration-300 group relative">
                        <!-- Clickable overlay -->
                        <a href="{{ route('admin.posts.edit', $post) }}" class="absolute inset-0 z-0 rounded-xl"></a>
                        
                        <!-- Thumbnail (50x50) -->
                        <div class="w-[50px] h-[50px] flex-shrink-0 rounded-lg overflow-hidden shadow-sm relative group-hover:shadow transition-shadow z-10 pointer-events-none">
                            <img src="{{ $post->cover_image_url ?: asset('img/placeholder.png') }}" alt="{{ $post->title['en'] ?? '' }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        </div>

                        <!-- Content Details -->
                        <div class="flex-1 flex flex-col justify-center min-w-0 z-10 pointer-events-none">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white truncate" title="{{ $post->localized_title }}">
                                {{ $post->localized_title ?: '---' }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @if($post->is_active)
                                    <span class="px-2 py-0.5 text-[10px] sm:text-[11px] uppercase tracking-wider font-bold rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">{{ __('Active') }}</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] sm:text-[11px] uppercase tracking-wider font-bold rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ __('Inactive') }}</span>
                                @endif
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 font-semibold">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    {{ number_format($post->views) }}
                                </span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 font-semibold">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    {{ $post->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Actions Dropdown -->
                        <div class="relative z-20 flex-shrink-0" x-data="{ open: false }">
                            <button @click.prevent="open = !open" @click.outside="open = false" type="button" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 class="absolute top-full mt-1 z-50 w-40 rounded-xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 rtl:left-0 ltr:right-0 focus:outline-none py-1 border border-slate-100 dark:border-slate-700" 
                                 style="display: none;">
                                
                                <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-brand-600 dark:hover:text-brand-400 transition-colors w-full text-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rtl:ml-3 ltr:mr-3 rtl-flip text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    {{ __('Edit') }}
                                </a>
                                
                                <button type="button" @click.prevent="open = false; deleteModalOpen = true; postToDelete = '{{ addslashes(($post->title['en'] ?? '') ?: ($post->title['ar'] ?? '')) }}'; deletePostUrl = '{{ route('admin.posts.destroy', $post) }}'" class="flex items-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors w-full text-left border-t border-slate-100 dark:border-slate-700/50 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rtl:ml-3 ltr:mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($posts->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif

        <!-- Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="deleteModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="px-6 pt-8 pb-6 sm:p-10">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-12 sm:w-12">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-4 text-center sm:mt-0 sm:ml-4 sm:rtl:ml-0 sm:rtl:mr-4 sm:text-left">
                                <h3 class="text-xl leading-6 font-black text-slate-900 dark:text-white" id="modal-title">
                                    {{ __('Delete Post') }}
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        {{ __('Are you sure you want to delete this post?') }} <br>
                                        <strong class="text-slate-800 dark:text-slate-200 mt-2 block" x-text="postToDelete"></strong>
                                        <span class="block mt-2 text-red-500">{{ __('This action cannot be undone.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 sm:px-10 sm:flex sm:flex-row-reverse border-t border-slate-100 dark:border-slate-700/50">
                        <form :action="deletePostUrl" method="POST" class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-glow-danger px-6 py-3 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:rtl:ml-0 sm:rtl:mr-3 sm:w-auto sm:text-sm transition-colors">
                                {{ __('Delete') }}
                            </button>
                        </form>
                        <button type="button" @click="deleteModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-6 py-3 bg-white dark:bg-slate-700 text-base font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
