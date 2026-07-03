/**
 * Editor.js Setup & Initialization
 */

window.imageViewer = function() {
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

// Custom independent Headings extending the base Header tool
if (typeof Header !== 'undefined') {
    window.Heading1 = class extends Header {
        static get toolbox() { return { ...super.toolbox, title: 'Heading 1', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
    };
    window.Heading2 = class extends Header {
        static get toolbox() { return { ...super.toolbox, title: 'Heading 2', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
    };
    window.Heading3 = class extends Header {
        static get toolbox() { return { ...super.toolbox, title: 'Heading 3', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
    };
    window.Heading4 = class extends Header {
        static get toolbox() { return { ...super.toolbox, title: 'Heading 4', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
    };
    window.Heading5 = class extends Header {
        static get toolbox() { return { ...super.toolbox, title: 'Heading 5', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
    };
    window.Heading6 = class extends Header {
        static get toolbox() { return { ...super.toolbox, title: 'Heading 6', icon: '<svg width="14" height="14" viewBox="0 0 24 24"><path d="M4 4h2v16H4V4zm14 0h2v16h-2V4zM8 11h8v2H8v-2z" fill="currentColor"/></svg>' }; }
    };
}

window.editorConfig = (holderId, placeholder, isRtl, initialData) => {
    // Read base tool config from global window variable
    let serverTools = window.EditorJsConfig || {};
    let tools = {};

    // Map server config to actual JS classes loaded via CDN
    if (typeof Header !== 'undefined') {
        if (serverTools.heading1) tools.heading1 = { ...serverTools.heading1, class: window.Heading1 };
        if (serverTools.heading2) tools.heading2 = { ...serverTools.heading2, class: window.Heading2 };
        if (serverTools.heading3) tools.heading3 = { ...serverTools.heading3, class: window.Heading3 };
        if (serverTools.heading4) tools.heading4 = { ...serverTools.heading4, class: window.Heading4 };
        if (serverTools.heading5) tools.heading5 = { ...serverTools.heading5, class: window.Heading5 };
        if (serverTools.heading6) tools.heading6 = { ...serverTools.heading6, class: window.Heading6 };
    }
    if (serverTools.quote && typeof Quote !== 'undefined') {
        tools.quote = { ...serverTools.quote, class: Quote };
    }
    if (serverTools.delimiter && typeof Delimiter !== 'undefined') {
        tools.delimiter = { ...serverTools.delimiter, class: Delimiter };
    }
    if (serverTools.embed && typeof Embed !== 'undefined') {
        tools.embed = { ...serverTools.embed, class: Embed };
    }
    if (serverTools.image && typeof ImageTool !== 'undefined') {
        let imageConfig = { ...serverTools.image.config };
        
        // Inject dynamic URL and CSRF Token
        imageConfig.endpoints = {
            byFile: window.EditorJsUploadRoute || ''
        };
        imageConfig.additionalRequestHeaders = {
            'X-CSRF-TOKEN': window.EditorJsCsrfToken || ''
        };
        
        tools.image = { ...serverTools.image, class: ImageTool, config: imageConfig };
    }
    
    if (serverTools.list && typeof List !== 'undefined') tools.list = { ...serverTools.list, class: List };
    if (serverTools.nestedList && typeof NestedList !== 'undefined') tools.nestedList = { ...serverTools.nestedList, class: NestedList };
    if (serverTools.checklist && typeof Checklist !== 'undefined') tools.checklist = { ...serverTools.checklist, class: Checklist };
    if (typeof Underline !== 'undefined') tools.underline = Underline;
    if (serverTools.marker && typeof Marker !== 'undefined') tools.Marker = { ...serverTools.marker, class: Marker };
    
    if (serverTools.button) {
        if (typeof AnyButton !== 'undefined') tools.button = { ...serverTools.button, class: AnyButton };
        else if (typeof Button !== 'undefined') tools.button = { ...serverTools.button, class: Button };
    }

    if (typeof AlignmentBlockTune !== 'undefined') {
        tools.alignment = {
            class: AlignmentBlockTune,
            config: {
                default: "left",
                blocks: {
                    header: 'left',
                    list: 'left'
                }
            },
        };
    }
    
    if (typeof ColorPlugin !== 'undefined') {
        tools.textColor = {
            class: ColorPlugin,
            config: {
                colorCollections: ['#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#00FFFF', '#FF00FF', '#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                defaultColor: '#8B5CF6',
                type: 'text',
                customPicker: true
            }
        };
    }

    let config = {
        holder: holderId,
        placeholder: placeholder,
        i18n: { direction: isRtl ? 'rtl' : 'ltr' },
        tools: tools,
        tunes: typeof AlignmentBlockTune !== 'undefined' ? ['alignment'] : []
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

window.syncEditors = async function(e) {
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
};
