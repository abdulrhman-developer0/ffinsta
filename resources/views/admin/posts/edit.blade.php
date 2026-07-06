<x-admin-layout>
    <x-slot name="title">{{ __('Edit Post') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black text-primary">{{ __('Edit Post') }}</h2>
            <a href="{{ route('admin.posts.index') }}"
                class="btn-secondary px-4 py-2 sm:px-5 rounded-xl font-bold shadow-sm text-sm sm:text-base">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <script>
        window.EditorJsConfig = @json(config('editorjs.tools', []));
        window.EditorJsUploadRoute = '{{ route('admin.posts.upload-media') }}';
        window.EditorJsCsrfToken = '{{ csrf_token() }}';
        window.EditorJsEnPlaceholder = '{{ __('Start writing in English...') }}';
        window.EditorJsArPlaceholder = '{{ __('ابدأ الكتابة بالعربية...') }}';
    </script>
    @viteReactRefresh
    @vite('resources/js/react/editor-mount.jsx')

    <div
        class="card-premium-glow rounded-3xl p-4 sm:p-6 lg:p-8 bg-surface border border-slate-200 dark:border-slate-800 shadow-xl">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" id="postForm"
            x-data="{ isSubmitting: false }" @submit="isSubmitting = true;">
            @csrf
            @method('PATCH')

            <div class="space-y-8 max-w-5xl mx-auto">

                <!-- Basic Info Section -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 sm:p-6 lg:p-8 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4 sm:space-y-6">
                    <h3 class="font-bold text-xl text-primary flex items-center gap-2 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Basic Information') }}
                    </h3>

                    <!-- Title Field -->
                    <div class="form-group" x-data="{ titleLang: 'en' }">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-3 sm:mb-2">
                            <label class="form-label text-base m-0">{{ __('Title') }}</label>
                            <div
                                class="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner border border-slate-200 dark:border-slate-700 w-full sm:w-fit">
                                <button type="button"
                                    @click="
                                    if(titleLang === 'ar' && !document.getElementById('title_en').value) {
                                        document.getElementById('title_en').value = document.getElementById('title_ar').value;
                                    }
                                    titleLang = 'en'
                                "
                                    :class="{ 'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': titleLang === 'en', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': titleLang !== 'en' }"
                                    class="w-1/2 sm:w-auto px-4 sm:px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center justify-center sm:justify-start gap-2">
                                    <span class="text-lg">🇬🇧</span> English
                                </button>
                                <button type="button"
                                    @click="
                                    if(titleLang === 'en' && !document.getElementById('title_ar').value) {
                                        document.getElementById('title_ar').value = document.getElementById('title_en').value;
                                    }
                                    titleLang = 'ar'
                                "
                                    :class="{ 'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': titleLang === 'ar', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': titleLang !== 'ar' }"
                                    class="w-1/2 sm:w-auto px-4 sm:px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center justify-center sm:justify-start gap-2">
                                    <span class="text-lg">🇸🇦</span> العربية
                                </button>
                            </div>
                        </div>
                        <div x-show="titleLang === 'en'">
                            <input type="text" name="title[en]" id="title_en"
                                value="{{ old('title.en', $post->title['en'] ?? '') }}"
                                class="form-input text-lg font-semibold py-3 rounded-xl border-slate-200 dark:border-slate-600 focus:ring-brand-500"
                                placeholder="{{ __('Enter post title here...') }}">
                        </div>
                        <div x-show="titleLang === 'ar'" style="display: none;">
                            <input type="text" name="title[ar]" id="title_ar"
                                value="{{ old('title.ar', $post->title['ar'] ?? '') }}"
                                class="form-input text-lg font-semibold py-3 rounded-xl border-slate-200 dark:border-slate-600 focus:ring-brand-500"
                                placeholder="{{ __('Enter post title here...') }}" dir="rtl">
                        </div>
                        <input type="hidden" name="content_last_activity" id="content_last_activity" value="">
                        @error('title.en')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        @error('title.ar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <!-- Slug Field -->
                        <div class="mt-4" x-show="titleLang === 'en'">
                            <label class="form-label text-sm m-0">{{ __('Custom Slug (English)') }}</label>
                            <input type="text" name="slug[en]" value="{{ old('slug.en', $post->slug['en'] ?? '') }}"
                                class="form-input mt-1 py-2 rounded-xl border-slate-200 dark:border-slate-600 focus:ring-brand-500"
                                placeholder="{{ __('Leave empty to auto-generate') }}">
                            @error('slug.en')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4" x-show="titleLang === 'ar'" style="display: none;">
                            <label class="form-label text-sm m-0">{{ __('Custom Slug (Arabic)') }}</label>
                            <input type="text" name="slug[ar]" value="{{ old('slug.ar', $post->slug['ar'] ?? '') }}"
                                class="form-input mt-1 py-2 rounded-xl border-slate-200 dark:border-slate-600 focus:ring-brand-500"
                                placeholder="{{ __('Leave empty to auto-generate') }}" dir="rtl">
                            @error('slug.ar')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Cover Image -->
                    <div class="form-group">
                        <label class="form-label text-base m-0 mb-2 block">{{ __('Cover Image') }}</label>
                        @php $cover = $post->getFirstMediaUrl('cover') ?: ($post->image ? Storage::url($post->image) : null); @endphp

                        <div x-data="imageViewer()" class="space-y-4">
                            <label
                                class="flex flex-col items-center justify-center w-full h-56 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-2xl cursor-pointer bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors relative overflow-hidden group">
                                <div x-show="!imageUrl && !'{{ $cover }}'"
                                    class="flex flex-col items-center justify-center pt-5 pb-6 text-secondary group-hover:text-brand-500 transition-colors">
                                    <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="mb-1 text-sm font-bold">{{ __('Click to upload') }}</p>
                                    <p class="text-xs text-slate-500">{{ __('PNG, JPG, GIF up to 5MB') }}</p>
                                </div>
                                <template x-if="imageUrl">
                                    <img :src="imageUrl" class="absolute inset-0 w-full h-full object-cover">
                                </template>
                                @if ($cover)
                                    <div x-show="!imageUrl" class="absolute inset-0 w-full h-full">
                                        <img src="{{ $cover }}" class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-sm font-bold">{{ __('Change Image') }}</span>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="image" id="image" class="hidden" accept="image/*"
                                    @change="fileChosen">
                            </label>
                            @error('image')
                                <p class="form-error text-xs">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div
                    class="bg-slate-50 dark:bg-slate-800/50 p-4 sm:p-6 lg:p-8 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4 sm:space-y-6">
                    <div>
                        <h3 class="font-bold text-xl text-primary flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Post Content') }}
                        </h3>
                        <p class="text-sm text-muted mb-4">{{ __('Type / to bring up the block menu like Notion') }}
                        </p>
                    </div>

                    <div class="form-group" wire:ignore>
                        <div id="react-editor-mount"
                            data-en="{{ old('content.en', is_string($editorContentEn) ? $editorContentEn : json_encode($editorContentEn)) }}"
                            data-ar="{{ old('content.ar', is_string($editorContentAr) ? $editorContentAr : json_encode($editorContentAr)) }}">
                        </div>
                        @error('content.en')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        @error('content.ar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Meta Settings Section -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 sm:p-6 lg:p-8 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4 sm:space-y-6"
                    x-data="{ metaLang: 'en' }">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-xl text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                            {{ __('Meta Settings') }}
                        </h3>
                        <div
                            class="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner border border-slate-200 dark:border-slate-700">
                            <button type="button" @click="metaLang = 'en'"
                                :class="{ 'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': metaLang === 'en', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': metaLang !== 'en' }"
                                class="px-4 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                                <span class="text-sm font-black text-slate-400">EN</span>
                            </button>
                            <button type="button" @click="metaLang = 'ar'"
                                :class="{ 'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': metaLang === 'ar', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': metaLang !== 'ar' }"
                                class="px-4 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                                <span class="text-sm font-black text-slate-400">AR</span>
                            </button>
                        </div>
                    </div>

                    <!-- Meta Title -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Meta Page Title') }}</label>
                        <input x-show="metaLang === 'en'" type="text" name="meta_title[en]"
                            value="{{ old('meta_title.en', $post->meta_title['en'] ?? '') }}"
                            class="form-input rounded-xl border-slate-200 dark:border-slate-600"
                            placeholder="SEO Title">
                        <input x-show="metaLang === 'ar'" style="display: none;" type="text"
                            name="meta_title[ar]" value="{{ old('meta_title.ar', $post->meta_title['ar'] ?? '') }}"
                            class="form-input rounded-xl border-slate-200 dark:border-slate-600" dir="rtl"
                            placeholder="عنوان SEO">
                    </div>

                    <!-- Meta Keywords -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Meta Keywords') }}</label>
                        <input x-show="metaLang === 'en'" type="text" name="meta_keywords[en]"
                            value="{{ old('meta_keywords.en', $post->meta_keywords['en'] ?? '') }}"
                            class="form-input rounded-xl border-slate-200 dark:border-slate-600"
                            placeholder="keyword1, keyword2...">
                        <input x-show="metaLang === 'ar'" style="display: none;" type="text"
                            name="meta_keywords[ar]"
                            value="{{ old('meta_keywords.ar', $post->meta_keywords['ar'] ?? '') }}"
                            class="form-input rounded-xl border-slate-200 dark:border-slate-600" dir="rtl"
                            placeholder="كلمة1, كلمة2...">
                    </div>

                    <!-- Meta Description -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Meta Description') }}</label>
                        <textarea x-show="metaLang === 'en'" name="meta_description[en]"
                            class="form-input rounded-xl border-slate-200 dark:border-slate-600" rows="3"
                            placeholder="SEO Description">{{ old('meta_description.en', $post->meta_description['en'] ?? '') }}</textarea>
                        <textarea x-show="metaLang === 'ar'" style="display: none;" name="meta_description[ar]"
                            class="form-input rounded-xl border-slate-200 dark:border-slate-600" rows="3" dir="rtl"
                            placeholder="وصف SEO">{{ old('meta_description.ar', $post->meta_description['ar'] ?? '') }}</textarea>
                    </div>

                    <!-- Custom Header Tags -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Custom Header Tags (<head>)') }}</label>
                        <textarea name="meta_header" class="form-input rounded-xl font-mono text-sm border-slate-200 dark:border-slate-600"
                            rows="3" dir="ltr" placeholder="<meta name='custom' content='...'>">{{ old('meta_header', $post->meta_header) }}</textarea>
                    </div>
                </div>

                <!-- Settings & Publish Section -->
                <div class="flex flex-col gap-6">

                    <!-- Publish Box -->
                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm flex flex-col justify-center">
                        <h3 class="font-bold text-lg mb-4 text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            {{ __('Publish') }}
                        </h3>

                        <label
                            class="flex items-center gap-3 cursor-pointer p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-brand-500 transition-colors mb-6 shadow-sm">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $post->is_active) ? 'checked' : '' }} class="peer sr-only">
                                <div
                                    class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500 shadow-inner">
                                </div>
                            </div>
                            <span
                                class="text-sm font-bold text-primary">{{ __('Published (Visible to public)') }}</span>
                        </label>

                        <button type="submit"
                            class="btn-primary w-full py-4 rounded-xl text-lg font-black shadow-glow flex items-center justify-center gap-2 mt-auto"
                            :disabled="isSubmitting">
                            <svg x-show="!isSubmitting" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <svg x-show="isSubmitting" style="display: none;"
                                class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span
                                x-text="isSubmitting ? '{{ __('Updating...') }}' : '{{ __('Update Post') }}'"></span>
                        </button>
                    </div>
                </div>
            </div>

    </div>
    </form>
    </div>

</x-admin-layout>
<script>
    function imageViewer() {
        return {
            imageUrl: '',
            fileChosen(event) {
                this.fileToDataUrl(event, src => this.imageUrl = src)
            },
            fileToDataUrl(event, callback) {
                if (!event.target.files.length) return
                let file = event.target.files[0],
                    reader = new FileReader()
                reader.readAsDataURL(file)
                reader.onload = e => callback(e.target.result)
            }
        }
    }

    // Global Error Catcher for debugging EditorJS
    window.addEventListener('error', function(e) {
        let div = document.createElement('div');
        div.style.position = 'fixed';
        div.style.top = '10px';
        div.style.right = '10px';
        div.style.background = 'red';
        div.style.color = 'white';
        div.style.padding = '20px';
        div.style.zIndex = '999999';
        div.innerText = 'Error: ' + e.message + ' at ' + e.filename + ':' + e.lineno;
        document.body.appendChild(div);
    });
    window.addEventListener('unhandledrejection', function(e) {
        let div = document.createElement('div');
        div.style.position = 'fixed';
        div.style.top = '50px';
        div.style.right = '10px';
        div.style.background = 'orange';
        div.style.color = 'white';
        div.style.padding = '20px';
        div.style.zIndex = '999999';
        div.innerText = 'Promise Rejection: ' + (e.reason && e.reason.stack ? e.reason.stack : e.reason);
        document.body.appendChild(div);
    });
</script>
