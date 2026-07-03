<x-public-layout>
    <x-slot name="title">{{ __('Our Blog') }} - {{ config('app.name') }}</x-slot>
    <x-slot name="meta_description">{{ __('Stay up to date with the latest news, features, and updates from ') . config('app.name') }}</x-slot>
    <x-slot name="meta_keywords">blog, news, updates, {{ config('app.name') }}</x-slot>

    <x-slot name="header">
        <div class="py-16 bg-surface-2 border-b border-subtle relative overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 -mt-16 -mr-16 text-brand-500 opacity-5">
                <svg width="400" height="400" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-primary tracking-tight">{{ __('Our Blog') }}</h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-secondary">{{ __('Stay up to date with the latest news, features, and updates.') }}</p>
                
                <!-- Hashtag Filters -->
                @if(isset($hashtags) && $hashtags->count() > 0)
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 {{ !isset($selectedHashtag) ? 'bg-brand-500 text-white shadow-glow' : 'bg-surface-3 text-secondary hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-900/20' }}">
                        {{ __('All Posts') }}
                    </a>
                    @foreach($hashtags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 {{ (isset($selectedHashtag) && $selectedHashtag->id === $tag->id) ? 'bg-brand-500 text-white shadow-glow' : 'bg-surface-3 text-secondary hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-900/20' }}">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-16 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->isEmpty())
                <div class="text-center py-24 bg-surface-2 rounded-3xl border border-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-muted mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="text-xl font-bold text-primary">{{ __('No posts found') }}</h3>
                    <p class="mt-2 text-secondary">{{ __('Check back later for new updates.') }}</p>
                </div>
            @else
                <div class="flex flex-col gap-12">
                    @foreach($posts as $index => $post)
                        <!-- List Post -->
                        <div class="group flex flex-col md:flex-row gap-8 items-center bg-surface-2 p-4 rounded-3xl border border-subtle hover:shadow-lg hover:border-brand-200 transition-all duration-300">
                            <div class="w-full md:w-1/3 aspect-video md:aspect-[4/3] rounded-2xl overflow-hidden flex-shrink-0 relative">
                                @if($post->cover_image_url)
                                    <a href="{{ route('blog.show', $post->localized_slug) }}" class="block w-full h-full">
                                        <img src="{{ $post->cover_image_url }}" alt="{{ $post->localized_title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    </a>
                                @else
                                    <div class="w-full h-full bg-brand-50 flex items-center justify-center">
                                        <svg class="h-10 w-10 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="w-full md:w-2/3 py-2 pr-4 rtl:pl-4 rtl:pr-0">
                                <div class="flex items-center gap-2 mb-3">
                                    @foreach($post->hashtags->take(3) as $tag)
                                        <a href="{{ route('blog.tag', $tag->slug) }}" class="text-xs font-bold text-brand hover:text-brand-600 transition-colors uppercase tracking-wide">#{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                                <a href="{{ route('blog.show', $post->localized_slug) }}" class="block group-hover:text-brand-600 transition-colors">
                                    <h3 class="text-2xl font-bold text-primary mb-3 leading-snug">{{ $post->localized_title }}</h3>
                                    <p class="text-secondary mb-6 line-clamp-2">{{ Str::limit($post->rendered_plain_text, 150) }}</p>
                                </a>
                                <div class="flex items-center gap-5 text-sm font-medium text-muted">
                                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> {{ $post->created_at->format('M d, Y') }}</span>
                                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg> {{ $post->views }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-16 flex flex-col items-center gap-6">
                    @if($posts->lastPage() > 1)
                        <div class="text-sm font-bold text-muted bg-brand-50 dark:bg-brand-900/20 px-5 py-2.5 rounded-full border border-brand-100 dark:border-brand-800 shadow-sm">
                            {{ app()->getLocale() === 'ar' ? "صفحة {$posts->currentPage()} من إجمالي {$posts->lastPage()} صفحات" : "Page {$posts->currentPage()} of {$posts->lastPage()}" }}
                        </div>
                    @endif
                    <div class="w-full flex justify-center">
                        {{ $posts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
