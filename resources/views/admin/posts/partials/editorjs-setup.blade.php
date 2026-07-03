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
    
    /* Hide Scrollbar for Popovers to fix blocks menu */
    .ce-popover__items::-webkit-scrollbar,
    .ce-popover__custom-content::-webkit-scrollbar,
    .ce-popover::-webkit-scrollbar {
        display: none;
    }
    .ce-popover__items, .ce-popover__custom-content, .ce-popover {
        -ms-overflow-style: none;
        scrollbar-width: none;
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
    .dark .ce-toolbox__button:hover, .ce-toolbox__button--active,
    .dark .ce-settings__button:hover, .ce-settings__button--active {
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

    const editorConfig = (holderId, placeholder, isRtl, initialData) => {
        let tools = {
            header: { 
                class: Header, 
                inlineToolbar: true, 
                config: { placeholder: 'Enter a heading', levels: [2, 3, 4, 5, 6], defaultLevel: 2 } 
            },
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
