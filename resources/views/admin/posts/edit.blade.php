<x-admin-layout>
    <x-slot name="title">{{ __('Edit Blog Post') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-primary">{{ __('Edit Blog Post') }}</h2>
            <a href="{{ route('admin.posts.index') }}" class="btn-secondary px-5 py-2 rounded-xl font-bold shadow-sm">
                {{ __('Back to Posts') }}
            </a>
        </div>
    </x-slot>

    <!-- Editor.js & Core Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/nested-list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/underline@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/editorjs-button@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/editorjs-drag-drop"></script>

    <script>
        // Custom Headings
        class Heading1 extends Header {
            static get toolbox() { return { ...super.toolbox, title: 'Heading 1', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
        }
        class Heading2 extends Header {
            static get toolbox() { return { ...super.toolbox, title: 'Heading 2', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
        }
        class Heading3 extends Header {
            static get toolbox() { return { ...super.toolbox, title: 'Heading 3', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
        }
        class Heading4 extends Header {
            static get toolbox() { return { ...super.toolbox, title: 'Heading 4', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
        }
        class Heading5 extends Header {
            static get toolbox() { return { ...super.toolbox, title: 'Heading 5', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
        }
    </script>
    
    <div class="card-premium-glow rounded-3xl p-6 sm:p-8 bg-surface border border-slate-200 dark:border-slate-800 shadow-xl">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" id="postForm" x-data="{ isSubmitting: false }" @submit="syncEditors(event); isSubmitting = true;">
            @csrf
            @method('PATCH')
            
            <div class="space-y-8 max-w-5xl mx-auto">
                
                <!-- Basic Info Section -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-6 sm:p-8 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-6">
                    <h3 class="font-bold text-xl text-primary flex items-center gap-2 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ __('Basic Information') }}
                    </h3>
                    
                    <!-- Title Field -->
                    <div class="form-group" x-data="{ titleLang: 'en' }">
                        <div class="flex justify-between items-center mb-2">
                            <label class="form-label text-base m-0">{{ __('Title') }}</label>
                            <div class="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner border border-slate-200 dark:border-slate-700 w-fit">
                                <button type="button" @click="
                                    if(titleLang === 'ar' && !document.getElementById('title_en').value) {
                                        document.getElementById('title_en').value = document.getElementById('title_ar').value;
                                    }
                                    titleLang = 'en'
                                " :class="{'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': titleLang === 'en', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': titleLang !== 'en'}" class="px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                                    <span class="text-lg">🇬🇧</span> English
                                </button>
                                <button type="button" @click="
                                    if(titleLang === 'en' && !document.getElementById('title_ar').value) {
                                        document.getElementById('title_ar').value = document.getElementById('title_en').value;
                                    }
                                    titleLang = 'ar'
                                " :class="{'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': titleLang === 'ar', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': titleLang !== 'ar'}" class="px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                                    <span class="text-lg">🇸🇦</span> العربية
                                </button>
                            </div>
                        </div>
                        <div x-show="titleLang === 'en'">
                            <input type="text" name="title[en]" id="title_en" value="{{ old('title.en', $post->title['en'] ?? '') }}" class="form-input text-lg font-semibold py-3 rounded-xl border-slate-200 dark:border-slate-600 focus:ring-brand-500" placeholder="{{ __('Enter post title here...') }}">
                        </div>
                        <div x-show="titleLang === 'ar'" style="display: none;">
                            <input type="text" name="title[ar]" id="title_ar" value="{{ old('title.ar', $post->title['ar'] ?? '') }}" class="form-input text-lg font-semibold py-3 rounded-xl border-slate-200 dark:border-slate-600 focus:ring-brand-500" placeholder="{{ __('Enter post title here...') }}" dir="rtl">
                        </div>
                        @error('title.en') <p class="form-error">{{ $message }}</p> @enderror
                        @error('title.ar') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Cover Image -->
                    <div class="form-group">
                        <label class="form-label text-base m-0 mb-2 block">{{ __('Cover Image') }}</label>
                        @php $cover = $post->getFirstMediaUrl('cover') ?: ($post->image ? Storage::url($post->image) : null); @endphp
                        
                        <div x-data="imageViewer()" class="space-y-4">
                            <label class="flex flex-col items-center justify-center w-full h-56 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-2xl cursor-pointer bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors relative overflow-hidden group">
                                <div x-show="!imageUrl && !'{{ $cover }}'" class="flex flex-col items-center justify-center pt-5 pb-6 text-secondary group-hover:text-brand-500 transition-colors">
                                    <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="mb-1 text-sm font-bold">{{ __('Click to upload') }}</p>
                                    <p class="text-xs text-slate-500">{{ __('PNG, JPG, GIF up to 5MB') }}</p>
                                </div>
                                <template x-if="imageUrl">
                                    <img :src="imageUrl" class="absolute inset-0 w-full h-full object-cover">
                                </template>
                                @if($cover)
                                <div x-show="!imageUrl" class="absolute inset-0 w-full h-full">
                                    <img src="{{ $cover }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-sm font-bold">{{ __('Change Image') }}</span>
                                    </div>
                                </div>
                                @endif
                                <input type="file" name="image" id="image" class="hidden" accept="image/*" @change="fileChosen">
                            </label>
                            @error('image') <p class="form-error text-xs">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-6 sm:p-8 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-6">
                    <div>
                        <h3 class="font-bold text-xl text-primary flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            {{ __('Post Content') }}
                        </h3>
                        <p class="text-sm text-muted mb-4">{{ __('Type / to bring up the block menu like Notion') }}</p>
                    </div>

                    <div class="form-group" wire:ignore x-data="{ contentLang: 'en' }">
                        <div class="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner border border-slate-200 dark:border-slate-700 w-fit mb-4">
                            <button type="button" @click="
                                if(contentLang === 'ar') window.syncEditorLanguage('ar', 'en');
                                contentLang = 'en';
                            " :class="{'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': contentLang === 'en', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': contentLang !== 'en'}" class="px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                                <span class="text-lg">🇬🇧</span> English
                            </button>
                            <button type="button" @click="
                                if(contentLang === 'en') window.syncEditorLanguage('en', 'ar');
                                contentLang = 'ar';
                            " :class="{'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600': contentLang === 'ar', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': contentLang !== 'ar'}" class="px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2">
                                <span class="text-lg">🇸🇦</span> العربية
                            </button>
                        </div>
                    
                    <!-- English Content -->
                    <div x-show="contentLang === 'en'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm">
                        <div id="editor_en" class="text-slate-800 dark:text-slate-200 p-6 sm:p-10 min-h-[500px]"></div>
                    </div>
                    <input type="hidden" name="content[en]" id="content_en" value="{{ old('content.en', is_string($editorContentEn) ? $editorContentEn : json_encode($editorContentEn)) }}">
                    
                    <!-- Arabic Content -->
                    <div x-show="contentLang === 'ar'" style="display: none;" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm">
                        <div id="editor_ar" class="text-slate-800 dark:text-slate-200 p-6 sm:p-10 min-h-[500px]" dir="rtl"></div>
                    </div>
                    <input type="hidden" name="content[ar]" id="content_ar" value="{{ old('content.ar', is_string($editorContentAr) ? $editorContentAr : json_encode($editorContentAr)) }}">
                    
                    @error('content.en') <p class="form-error">{{ $message }}</p> @enderror
                    @error('content.ar') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    </div>
                </div>

                <!-- Settings & Publish Section -->
                <div class="flex flex-col gap-6">
                    <!-- Modern Hashtags Input -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm">
                        <h3 class="font-bold text-lg mb-4 text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>
                            {{ __('Tags') }}
                        </h3>
                        
                        <div x-data="{ 
                                tags: [], 
                                newTag: '',
                                availableTags: @js($existingHashtags ?? []),
                                isOpen: false,
                                get filteredTags() {
                                    let search = this.newTag.trim().toLowerCase();
                                    let matches = this.availableTags.filter(t => !this.tags.includes(t) && t.toLowerCase().includes(search));
                                    if (search !== '' && !this.tags.includes(this.newTag.trim()) && !matches.find(m => m.toLowerCase() === search)) {
                                        matches.unshift(this.newTag.trim());
                                    }
                                    return search === '' ? [] : matches;
                                },
                                init() {
                                    let oldTags = '{{ old('hashtags', $hashtags ?? '') }}';
                                    if(oldTags) {
                                        this.tags = oldTags.split(',').map(t => t.trim()).filter(t => t);
                                    }
                                    this.$watch('newTag', value => {
                                        this.isOpen = value.trim() !== '';
                                    });
                                },
                                addTag(tag) {
                                    if(tag && tag.trim() !== '' && !this.tags.includes(tag.trim())) {
                                        this.tags.push(tag.trim());
                                    }
                                    this.newTag = '';
                                    this.isOpen = false;
                                    this.$refs.tagInput.focus();
                                },
                                removeTag(index) {
                                    this.tags.splice(index, 1);
                                }
                            }" 
                            class="space-y-4" @click.away="isOpen = false">
                            
                            <!-- Inline Tags Container -->
                            <div class="form-input w-full p-2 min-h-[52px] rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 shadow-sm flex flex-wrap gap-2 items-center cursor-text transition-colors focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500"
                                 @click="$refs.tagInput.focus()">
                                 
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-sm font-semibold border border-brand-200 dark:border-brand-700/50 animate-fade-in select-none">
                                        <span x-text="tag"></span>
                                        <button type="button" @click.stop="removeTag(index)" class="text-brand-400 hover:text-brand-700 dark:hover:text-brand-200 focus:outline-none transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>

                                <!-- Input Field with Dropdown -->
                                <div class="relative flex-1 min-w-[150px]">
                                    <input type="text" 
                                           x-ref="tagInput"
                                           x-model="newTag" 
                                           @keydown.enter.prevent="if(filteredTags.length > 0) addTag(filteredTags[0])"
                                           @keydown.backspace="if(newTag === '' && tags.length > 0) removeTag(tags.length - 1)"
                                           @keydown.arrow-down.prevent="isOpen = true"
                                           @focus="isOpen = newTag.trim() !== ''"
                                           class="w-full bg-transparent border-0 focus:ring-0 p-0 text-sm font-medium text-slate-800 dark:text-slate-200 placeholder-slate-400" 
                                           placeholder="{{ __('Type a tag and press Enter...') }}">
                                    
                                    <div x-show="isOpen && filteredTags.length > 0" 
                                         style="display: none;"
                                         class="absolute left-0 top-full z-50 w-64 mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                        <ul class="py-1">
                                            <template x-for="(suggest, idx) in filteredTags" :key="idx">
                                                <li @click="addTag(suggest)" 
                                                    class="px-4 py-2.5 cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-900/30 text-sm font-semibold transition-colors flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 last:border-0"
                                                    :class="{'text-brand-600 dark:text-brand-400 bg-brand-50/50 dark:bg-brand-900/10': idx === 0 && suggest === newTag.trim()}">
                                                    <span class="flex items-center gap-2"><span class="text-brand-400 opacity-50">#</span><span x-text="suggest"></span></span>
                                                    <span x-show="idx === 0 && suggest === newTag.trim()" class="text-xs bg-brand-100 dark:bg-brand-900 text-brand-600 dark:text-brand-400 px-2 py-0.5 rounded">{{ __('Create New') }}</span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="hashtags" :value="tags.join(',')">
                            <p class="text-xs text-muted font-medium mt-2">{{ __('Type to search existing tags or press Enter to create a new one. Press Backspace to delete.') }}</p>
                            @error('hashtags') <p class="form-error text-xs">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Publish Box -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm flex flex-col justify-center">
                        <h3 class="font-bold text-lg mb-4 text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                            {{ __('Publish') }}
                        </h3>
                        
                        <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-brand-500 transition-colors mb-6 shadow-sm">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $post->is_active) ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500 shadow-inner"></div>
                            </div>
                            <span class="text-sm font-bold text-primary">{{ __('Published (Visible to public)') }}</span>
                        </label>

                        <button type="submit" class="btn-primary w-full py-4 rounded-xl text-lg font-black shadow-glow flex items-center justify-center gap-2 mt-auto" :disabled="isSubmitting">
                            <svg x-show="!isSubmitting" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <svg x-show="isSubmitting" style="display: none;" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isSubmitting ? '{{ __('Updating...') }}' : '{{ __('Update Post') }}'"></span>
                        </button>
                    </div>
                </div>
                
            </div>
        </form>
    </div>
    
    <style>
        /* Editor.js Custom Theme Styling */
        .ce-block__content, .ce-toolbar__content { max-width: calc(100% - 80px) !important; }
        .codex-editor { height: 100%; min-height: 400px; }
        .codex-editor__redactor { padding-bottom: 300px !important; }
        .ce-header { font-weight: 800; color: inherit; }
        h1.ce-header { font-size: 2.25em; line-height: 1.2; margin-top: 1.5em; margin-bottom: 0.5em; }
        h2.ce-header { font-size: 1.5em; line-height: 1.3; margin-top: 1.5em; margin-bottom: 0.5em; }
        h3.ce-header { font-size: 1.25em; line-height: 1.4; margin-top: 1.5em; margin-bottom: 0.5em; }
        h4.ce-header { font-size: 1.125em; line-height: 1.5; margin-top: 1.5em; margin-bottom: 0.5em; }
        h5.ce-header { font-size: 1em; line-height: 1.5; margin-top: 1.5em; margin-bottom: 0.5em; }
        h6.ce-header { font-size: 0.875em; line-height: 1.5; margin-top: 1.5em; margin-bottom: 0.5em; }
        .ce-paragraph { line-height: 1.8; color: inherit; }
        
        /* Lists */
        .cdx-list { padding-left: 2rem !important; list-style-type: decimal !important; }
        .cdx-list--unordered { list-style-type: disc !important; }
        [dir="rtl"] .cdx-list { padding-left: 0 !important; padding-right: 2rem !important; }

        /* Selection Color */
        .codex-editor__redactor ::selection {
            background-color: #2055f5 !important;
            color: #ffffff !important;
        }
        .dark .codex-editor__redactor ::selection {
            background-color: #4d80ff !important;
            color: #ffffff !important;
        }
        
        /* General Popups & Toolboxes */
        .ce-popover, .ce-toolbox, .ce-settings, .tc-popover {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            padding: 8px !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
        }
        
        .ce-popover-item, .ce-toolbox__button, .ce-settings__button {
            border-radius: 10px !important;
            transition: all 0.2s ease !important;
            margin-bottom: 2px !important;
            color: #1e293b !important;
        }
        
        .ce-popover-item:hover, .ce-popover-item--active, .ce-popover-item--focused,
        .ce-toolbox__button:hover, .ce-toolbox__button--active,
        .ce-settings__button:hover, .ce-settings__button--active {
            background-color: #f8fafc !important;
            color: #8b5cf6 !important;
        }
        
        .ce-popover-item__icon, .ce-toolbox__button .ce-icon, .ce-settings__button .ce-icon {
            border-radius: 8px !important;
            background-color: #ffffff !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            border: 1px solid #f1f5f9 !important;
            color: currentColor !important;
        }

        /* Inline Toolbar */
        .ce-inline-toolbar {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            background-color: #ffffff !important;
        }
        .ce-inline-tool { transition: all 0.2s ease; border-radius: 6px !important; margin: 0 2px !important; color: #1e293b !important; }
        .ce-inline-tool:hover { background-color: #f1f5f9 !important; color: #8b5cf6 !important; }

        /* Left Toolbar Buttons (+ and drag) */
        .ce-toolbar__plus, .ce-toolbar__settings-btn {
            border-radius: 8px !important;
            transition: all 0.2s ease;
            color: #64748b !important;
        }
        .ce-toolbar__plus:hover, .ce-toolbar__settings-btn:hover {
            background-color: #f1f5f9 !important;
            color: #8b5cf6 !important;
        }

        /* ---------------- Dark Mode Overrides ---------------- */
        .dark .ce-block { color: #f8fafc !important; }
        .dark .ce-toolbar__actions { background-color: transparent !important; }
        
        /* Dark Popovers & Toolboxes */
        .dark .ce-popover, .dark .ce-toolbox, .dark .ce-settings, .dark .tc-popover {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5) !important;
            color: #f8fafc !important;
        }
        
        .dark .ce-popover-item, .dark .ce-toolbox__button, .dark .ce-settings__button {
            color: #e2e8f0 !important;
        }
        
        .dark .ce-popover-item:hover, .dark .ce-popover-item--active, .dark .ce-popover-item--focused,
        .dark .ce-toolbox__button:hover, .dark .ce-toolbox__button--active,
        .dark .ce-settings__button:hover, .dark .ce-settings__button--active {
            background-color: #334155 !important;
            color: #a78bfa !important;
        }
        
        .dark .ce-popover-item__icon, .dark .ce-toolbox__button .ce-icon, .dark .ce-settings__button .ce-icon {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
            box-shadow: none !important;
        }
        
        .dark .ce-popover-item__title { color: #f8fafc !important; }

        /* Dark Inline Toolbar */
        .dark .ce-inline-toolbar {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        .dark .ce-inline-tool { color: #cbd5e1 !important; }
        .dark .ce-inline-tool:hover { background-color: #334155 !important; color: #a78bfa !important; }

        /* Dark Inputs, Search & Misc */
        .dark .cdx-input, .dark .ce-inline-toolbar__dropdown {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
            box-shadow: none !important;
        }
        
        .dark .ce-popover input, 
        .dark .cdx-search-field, 
        .dark .cdx-search-field__input,
        .dark .ce-popover-header__search {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
            box-shadow: none !important;
        }
        
        .dark .cdx-search-field__icon svg, .dark .ce-popover-header__search svg {
            fill: #94a3b8 !important;
            color: #94a3b8 !important;
        }
        
        .dark .cdx-search-field__input::placeholder, .dark .ce-popover input::placeholder {
            color: #64748b !important;
        }
        
        .dark .ce-toolbar__plus, .dark .ce-toolbar__settings-btn { color: #94a3b8 !important; }
        .dark .ce-toolbar__plus:hover, .dark .ce-toolbar__settings-btn:hover {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }
        .dark .cdx-button {
            background-color: #1e293b !important;
            color: #fff !important;
            border-color: #334155 !important;
        }
        
        .cdx-button { border-radius: 0.75rem !important; font-weight: bold !important; }
        .ce-delimiter { color: #94a3b8 !important; }
        
        /* Force CSS Variable Overrides for Editor.js ^2.29+ */
        html.dark, .dark .codex-editor, .dark .ce-popover {
            --color-background: #1e293b !important;
            --color-text-primary: #f8fafc !important;
            --color-border: #334155 !important;
            --color-shadow: rgba(0,0,0,0.5) !important;
            --color-text-secondary: #94a3b8 !important;
            --color-border-icon: #334155 !important;
            --color-border-icon-disabled: #475569 !important;
            --color-background-icon: #0f172a !important;
            --color-background-item-hover: #334155 !important;
            --color-background-item-focus: #334155 !important;
            --color-background-item-confirm: #dc2626 !important;
            --color-background-item-confirm-hover: #b91c1c !important;
            --color-background-icon-active: #8b5cf6 !important;
            --color-text-icon-active: #ffffff !important;
            --color-gray-border: #334155 !important;
            --color-line-gray: #334155 !important;
            --color-active-icon: #8b5cf6 !important;
        }
    </style>

    <script>
        function imageViewer() {
            return {
                imageUrl: '',
                fileChosen(event) {
                    if (event.target.files.length === 0) return;
                    let file = event.target.files[0];
                    let reader = new FileReader();
                    reader.onload = (e) => this.imageUrl = e.target.result;
                    reader.readAsDataURL(file);
                }
            }
        }

        let dataEn = null;
        let dataAr = null;
        try { dataEn = {!! json_encode(old('content.en', $editorContentEn)) !!}; } catch(e) {}
        try { dataAr = {!! json_encode(old('content.ar', $editorContentAr)) !!}; } catch(e) {}

        const editorConfig = (holderId, placeholder, isRtl, initialData) => {
            let tools = {
                heading1: { class: Heading1, inlineToolbar: true, config: { placeholder: 'Heading 1', levels: [2], defaultLevel: 2 } },
                heading2: { class: Heading2, inlineToolbar: true, config: { placeholder: 'Heading 2', levels: [3], defaultLevel: 3 } },
                heading3: { class: Heading3, inlineToolbar: true, config: { placeholder: 'Heading 3', levels: [4], defaultLevel: 4 } },
                heading4: { class: Heading4, inlineToolbar: true, config: { placeholder: 'Heading 4', levels: [5], defaultLevel: 5 } },
                heading5: { class: Heading5, inlineToolbar: true, config: { placeholder: 'Heading 5', levels: [6], defaultLevel: 6 } },
                quote: {
                    class: Quote,
                    inlineToolbar: true,
                },
                delimiter: {
                    class: Delimiter,
                },
                embed: {
                    class: Embed,
                    config: {
                        services: {
                            youtube: true,
                            vimeo: true,
                            instagram: true,
                            twitter: true,
                        }
                    }
                },
                image: {
                    class: ImageTool,
                    config: {
                        endpoints: {
                            byFile: '{{ route('admin.posts.upload-media') }}', 
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }
                }
            };
            
            if (typeof List !== 'undefined') tools.list = { class: List, inlineToolbar: true };
            if (typeof NestedList !== 'undefined') tools.nestedList = { class: NestedList, inlineToolbar: true };
            if (typeof Checklist !== 'undefined') tools.checklist = { class: Checklist, inlineToolbar: true };
            if (typeof Underline !== 'undefined') tools.underline = Underline;
            if (typeof Marker !== 'undefined') tools.Marker = { class: Marker, shortcut: 'CMD+SHIFT+M' };
            if (typeof AnyButton !== 'undefined') tools.button = { class: AnyButton, inlineToolbar: false };
            else if (typeof Button !== 'undefined') tools.button = { class: Button, inlineToolbar: false };

            let config = {
                holder: holderId,
                placeholder: placeholder,
                i18n: { direction: isRtl ? 'rtl' : 'ltr' },
                tools: tools
            };
            
            if (initialData && initialData.blocks && initialData.blocks.length > 0) {
                config.data = initialData;
            }
            
            return config;
        };

        window.editorEn = new EditorJS(editorConfig('editor_en', @json(__('Let\'s write an awesome story...')), false, dataEn));
        window.editorAr = new EditorJS(editorConfig('editor_ar', @json(__('Let\'s write an awesome story...')), true, dataAr));

        window.editorEn.isReady.then(() => { new DragDrop(window.editorEn); });
        window.editorAr.isReady.then(() => { new DragDrop(window.editorAr); });

        window.syncEditorLanguage = async function(from, to) {
            try {
                const fromEditor = from === 'en' ? window.editorEn : window.editorAr;
                const toEditor = to === 'en' ? window.editorEn : window.editorAr;
                
                const toData = await toEditor.save();
                if (!toData.blocks || toData.blocks.length === 0) {
                    const fromData = await fromEditor.save();
                    if (fromData.blocks && fromData.blocks.length > 0) {
                        toEditor.render(fromData);
                    }
                }
            } catch (e) {
                console.error("Language sync failed", e);
            }
        };

        async function syncEditors(e) {
            try {
                const enData = await window.editorEn.save();
                const arData = await window.editorAr.save();
                
                document.getElementById('content_en').value = JSON.stringify(enData);
                document.getElementById('content_ar').value = JSON.stringify(arData);
            } catch (error) {
                console.error('Saving failed: ', error);
                e.preventDefault();
                alert('An error occurred while preparing post data.');
            }
        }
    </script>
</x-admin-layout>
