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

    @include('admin.posts.partials.editorjs-setup')

    <script>
        let dataEn = null;
        let dataAr = null;
        try { dataEn = {!! json_encode(old('content.en', $editorContentEn)) !!}; } catch(e) {}
        try { dataAr = {!! json_encode(old('content.ar', $editorContentAr)) !!}; } catch(e) {}

        window.editorEn = new EditorJS(editorConfig('editor_en', @json(__('Let\'s write an awesome story...')), false, dataEn));
        window.editorAr = new EditorJS(editorConfig('editor_ar', @json(__('Let\'s write an awesome story...')), true, dataAr));

        window.editorEn.isReady.then(() => { new DragDrop(window.editorEn); });
        window.editorAr.isReady.then(() => { new DragDrop(window.editorAr); });
    </script>
</x-admin-layout>
