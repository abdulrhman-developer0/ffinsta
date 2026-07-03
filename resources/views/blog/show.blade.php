@php
    $tagsStr = $post->hashtags->pluck('name')->implode(', ');
@endphp
<x-public-layout>
    <x-slot name="title">{{ $post->localized_title }} - {{ config('app.name') }}</x-slot>
    <x-slot name="meta_description">{{ Str::limit($plainText, 160) }}</x-slot>
    <x-slot name="meta_keywords">{{ $tagsStr }}</x-slot>
    @if($post->cover_image_url)
        <x-slot name="og_image">{{ Str::startsWith($post->cover_image_url, 'http') ? $post->cover_image_url : asset($post->cover_image_url) }}</x-slot>
    @endif
    <x-slot name="header">
        <!-- We will not use the standard header for show page, to allow image full width -->
    </x-slot>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-4 flex-wrap">
                @foreach($post->hashtags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="badge bg-brand-100 text-brand-700 hover:bg-brand-200 transition-colors px-3 py-1 text-sm">#{{ $tag->name }}</a>
                @endforeach
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold text-primary tracking-tight leading-tight mb-4">
                {{ $post->localized_title }}
            </h1>
            <div class="flex items-center justify-center gap-4 text-sm text-muted">
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $post->created_at->format('F d, Y') }}
                </span>
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ $post->views }} {{ __('Views') }}
                </span>
            </div>
        </div>

        @if($post->cover_image_url)
            <div class="mb-12 rounded-3xl overflow-hidden shadow-card">
                <img src="{{ $post->cover_image_url }}" alt="{{ $post->localized_title }}" class="w-full h-auto max-h-[600px] object-cover">
            </div>
        @endif

        <div class="prose prose-lg dark:prose-invert max-w-none mx-auto text-primary">
            {!! $renderedContent !!}
        </div>
        
        <div class="mt-16 pt-8 border-t border-subtle text-center">
            <a href="{{ route('blog.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to Blog') }}
            </a>
        </div>
    </article>

    @push('styles')
    <style>
        .cdx-marker {
            background-color: rgba(255, 235, 160, 0.5); /* Soft yellow highlight */
            padding: 0 4px;
            border-radius: 4px;
            color: inherit;
        }
        .dark .cdx-marker {
            background-color: rgba(234, 179, 8, 0.3); /* Dark mode yellow highlight */
        }
    </style>
    @endpush
</x-public-layout>
