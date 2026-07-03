/**
 * Editor.js Setup & Initialization
 */

    }
}
window.editorConfig = (holderId, placeholder, isRtl, initialData) => {
    // Read base tool config from global window variable
    let serverTools = window.EditorJsConfig || {};
    let tools = {};

    // Map server config to actual JS classes loaded via CDN
    if (serverTools.header && typeof Header !== 'undefined') { tools.header = { ...serverTools.header, class: Header }; }
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
    
        let config = { holder: holderId, placeholder: placeholder, i18n: { direction: isRtl ? "rtl" : "ltr" }, tools: tools };
    
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



