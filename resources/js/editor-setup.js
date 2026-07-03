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
// Custom Emoji Inline Tool
class EmojiInlineTool {
    static get isInline() { return true; }
    static get title() { return 'Emoji'; }
    
    constructor({ api }) {
        this.api = api;
        this.button = null;
        this.pickerContainer = null;
        this.state = false;
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.innerHTML = '😀';
        this.button.classList.add(this.api.styles.inlineToolButton);
        return this.button;
    }

    surround(range) {
        if (!this.state) {
            this.showPicker(range);
        } else {
            this.hidePicker();
        }
    }
    
    showPicker(range) {
        this.state = true;
        this.button.classList.add(this.api.styles.inlineToolButtonActive);
        
        this.pickerContainer = document.createElement('div');
        this.pickerContainer.style.position = 'absolute';
        this.pickerContainer.style.zIndex = '9999';
        this.pickerContainer.style.backgroundColor = 'white';
        this.pickerContainer.style.boxShadow = '0 10px 15px -3px rgb(0 0 0 / 0.1)';
        this.pickerContainer.style.borderRadius = '0.75rem';
        this.pickerContainer.style.overflow = 'hidden';
        
        const picker = document.createElement('emoji-picker');
        picker.addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            const textNode = document.createTextNode(emoji);
            range.insertNode(textNode);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            this.hidePicker();
        });
        
        this.pickerContainer.appendChild(picker);
        document.body.appendChild(this.pickerContainer);
        
        const rect = this.button.getBoundingClientRect();
        this.pickerContainer.style.top = (rect.bottom + window.scrollY + 10) + 'px';
        this.pickerContainer.style.left = rect.left + 'px';
        
        this.closeHandler = (e) => {
            if (!this.pickerContainer.contains(e.target) && !this.button.contains(e.target)) {
                this.hidePicker();
            }
        };
        setTimeout(() => document.addEventListener('click', this.closeHandler), 10);
    }
    
    hidePicker() {
        this.state = false;
        this.button.classList.remove(this.api.styles.inlineToolButtonActive);
        if (this.pickerContainer) {
            this.pickerContainer.remove();
            this.pickerContainer = null;
        }
        if (this.closeHandler) {
            document.removeEventListener('click', this.closeHandler);
        }
    }

    checkState() { return this.state; }
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
    
    // Add our custom emoji inline tool
    tools.emoji = { class: EmojiInlineTool };

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

