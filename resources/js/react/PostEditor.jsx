import React, { useEffect, useRef, useState } from 'react';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import ImageTool from '@editorjs/image';
import List from '@editorjs/list';
import NestedList from '@editorjs/nested-list';
import Quote from '@editorjs/quote';
import Embed from '@editorjs/embed';
import Delimiter from '@editorjs/delimiter';
import Underline from '@editorjs/underline';
import Marker from '@editorjs/marker';
import Button from 'editorjs-button';
import 'emoji-picker-element';

import ColorPlugin from 'editorjs-text-color-plugin';
import AlignmentBlockTune from 'editorjs-text-alignment-blocktune';

const createHeadingClass = (iconHtml, title) => {
    return class extends Header {
        static get toolbox() {
            return {
                icon: iconHtml,
                title: title
            };
        }
    };
};

const Heading1 = createHeadingClass('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h8m-8 6V6m8 12V6m7 6h-3v6m3-6a3 3 0 100-6 3 3 0 000 6z"/></svg>', 'Heading 1');
const Heading2 = createHeadingClass('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h8m-8 6V6m8 12V6m7 6h-3v6m3-6a3 3 0 100-6 3 3 0 000 6z"/></svg>', 'Heading 2');
const Heading3 = createHeadingClass('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h8m-8 6V6m8 12V6m7 6h-3v6m3-6a3 3 0 100-6 3 3 0 000 6z"/></svg>', 'Heading 3');
const Heading4 = createHeadingClass('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h8m-8 6V6m8 12V6m7 6h-3v6m3-6a3 3 0 100-6 3 3 0 000 6z"/></svg>', 'Heading 4');
const Heading5 = createHeadingClass('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h8m-8 6V6m8 12V6m7 6h-3v6m3-6a3 3 0 100-6 3 3 0 000 6z"/></svg>', 'Heading 5');

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

const buildToolsConfig = (isRtl) => {
    let serverTools = window.EditorJsConfig || {};
    let tools = {};
    
    tools.alignment = {
        class: AlignmentBlockTune,
        config: {
            default: isRtl ? "right" : "left"
        },
    };
    
    class SafeColorPlugin extends ColorPlugin {
        constructor(args) {
            if (!args.config || Object.keys(args.config).length === 0) {
                args.config = {
                    colorCollections: ['#1e293b', '#64748b', '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef', '#f43f5e'],
                    defaultColor: '#1e293b',
                    type: 'text',
                    customPicker: true
                };
            }
            super(args);
        }
    }

    tools.Color = {
        class: SafeColorPlugin,
        config: {
           colorCollections: ['#1e293b', '#64748b', '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef', '#f43f5e'],
           defaultColor: '#1e293b',
           type: 'text',
           customPicker: true
        }
    };

    if (serverTools.heading1) tools.heading1 = { ...serverTools.heading1, class: Heading1, config: { defaultLevel: 2, levels: [2] }, tunes: ['alignment'] };
    if (serverTools.heading2) tools.heading2 = { ...serverTools.heading2, class: Heading2, config: { defaultLevel: 3, levels: [3] }, tunes: ['alignment'] };
    if (serverTools.heading3) tools.heading3 = { ...serverTools.heading3, class: Heading3, config: { defaultLevel: 4, levels: [4] }, tunes: ['alignment'] };
    if (serverTools.heading4) tools.heading4 = { ...serverTools.heading4, class: Heading4, config: { defaultLevel: 5, levels: [5] }, tunes: ['alignment'] };
    if (serverTools.heading5) tools.heading5 = { ...serverTools.heading5, class: Heading5, config: { defaultLevel: 6, levels: [6] }, tunes: ['alignment'] };
    
    // Fallback if they still use 'header'
    if (serverTools.header && !serverTools.heading1) tools.header = { ...serverTools.header, class: Header, tunes: ['alignment'] };
    
    if (serverTools.quote) tools.quote = { ...serverTools.quote, class: Quote, tunes: ['alignment'] };
    if (serverTools.delimiter) tools.delimiter = { ...serverTools.delimiter, class: Delimiter };
    if (serverTools.embed) tools.embed = { ...serverTools.embed, class: Embed, tunes: ['alignment'] };
    if (serverTools.image) {
        let imageConfig = { ...serverTools.image.config };
        imageConfig.endpoints = { byFile: window.EditorJsUploadRoute || '' };
        imageConfig.additionalRequestHeaders = { 'X-CSRF-TOKEN': window.EditorJsCsrfToken || '' };
        tools.image = { ...serverTools.image, class: ImageTool, config: imageConfig };
    }
    if (serverTools.list) tools.list = { ...serverTools.list, class: List };
    
    tools.paragraph = {
        tunes: ['alignment']
    };
    
    tools.underline = Underline;
    if (serverTools.marker) tools.marker = { ...serverTools.marker, class: Marker };
    if (serverTools.button) tools.button = { ...serverTools.button, class: Button, tunes: ['alignment'] };
    
    tools.emoji = { class: EmojiInlineTool };

    
    return tools;
};

export default function PostEditor({ initialDataEn, initialDataAr }) {
    const [lang, setLang] = useState('en');
    const editorEnRef = useRef(null);
    const editorArRef = useRef(null);

    useEffect(() => {
        // Ensure hidden inputs exist
        let inputEn = document.getElementById('content_en');
        let inputAr = document.getElementById('content_ar');
        if (!inputEn) {
            inputEn = document.createElement('input');
            inputEn.type = 'hidden';
            inputEn.name = 'content[en]';
            inputEn.id = 'content_en';
            document.getElementById('react-editor-mount').parentNode.appendChild(inputEn);
        }
        if (!inputAr) {
            inputAr = document.createElement('input');
            inputAr.type = 'hidden';
            inputAr.name = 'content[ar]';
            inputAr.id = 'content_ar';
            document.getElementById('react-editor-mount').parentNode.appendChild(inputAr);
        }

        const handleSave = async (api, inputElement) => {
            try {
                const data = await api.saver.save();
                inputElement.value = JSON.stringify(data);
                
                // Track edit activity
                let activityInput = document.getElementById('content_last_activity');
                if (activityInput) {
                    activityInput.value = Date.now().toString();
                }
            } catch (e) {
                console.error('Saving failed: ', e);
            }
        };

        const editorEn = new EditorJS({
            holder: 'editor_en_container',
            placeholder: window.EditorJsEnPlaceholder || 'Start writing in English...',
            i18n: { direction: 'ltr' },
            data: initialDataEn,
            tools: buildToolsConfig(false),
            onChange: (api) => handleSave(api, inputEn)
        });
        editorEnRef.current = editorEn;

        const editorAr = new EditorJS({
            holder: 'editor_ar_container',
            placeholder: window.EditorJsArPlaceholder || 'ابدأ الكتابة بالعربية...',
            i18n: { direction: 'rtl' },
            data: initialDataAr,
            tools: buildToolsConfig(true),
            onChange: (api) => handleSave(api, inputAr)
        });
        editorArRef.current = editorAr;

        return () => {
            if (editorEnRef.current && editorEnRef.current.destroy) {
                try { editorEnRef.current.destroy(); } catch (e) {}
                editorEnRef.current = null;
            }
            if (editorArRef.current && editorArRef.current.destroy) {
                try { editorArRef.current.destroy(); } catch (e) {}
                editorArRef.current = null;
            }
        };
    }, []);

    const syncLanguage = async (from, to) => {
        try {
            const fromEditor = from === 'en' ? editorEnRef.current : editorArRef.current;
            const toEditor = to === 'en' ? editorEnRef.current : editorArRef.current;
            
            if (!fromEditor || !toEditor) return;
            
            const toData = await toEditor.save();
            if (!toData.blocks || toData.blocks.length === 0) {
                const fromData = await fromEditor.save();
                if (fromData.blocks && fromData.blocks.length > 0) {
                    await toEditor.render(fromData);
                }
            }
        } catch (e) {
            console.error("Error syncing editor language: ", e);
        }
    };

    const handleSwitchLang = (newLang) => {
        if (lang !== newLang) {
            syncLanguage(lang, newLang);
            setLang(newLang);
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <div className="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 shadow-inner border border-slate-200 dark:border-slate-700 w-fit">
                    <button 
                        type="button" 
                        onClick={() => handleSwitchLang('en')}
                        className={`px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2 ${
                            lang === 'en' 
                            ? 'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600' 
                            : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
                        }`}
                    >
                        <span className="text-sm font-black text-slate-500 dark:text-slate-400">EN</span>
                    </button>
                    <button 
                        type="button" 
                        onClick={() => handleSwitchLang('ar')}
                        className={`px-5 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-2 ${
                            lang === 'ar' 
                            ? 'bg-white dark:bg-slate-700 shadow-sm text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-600' 
                            : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
                        }`}
                    >
                        <span className="text-sm font-black text-slate-500 dark:text-slate-400">AR</span>
                    </button>
                </div>
            </div>

            <div style={{ display: lang === 'en' ? 'block' : 'none' }} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm">
                <div id="editor_en_container" className="text-slate-800 dark:text-slate-200 p-6 sm:p-10 min-h-[500px]"></div>
            </div>
            
            <div style={{ display: lang === 'ar' ? 'block' : 'none' }} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm">
                <div id="editor_ar_container" className="text-slate-800 dark:text-slate-200 p-6 sm:p-10 min-h-[500px]" dir="rtl"></div>
            </div>
        </div>
    );
}
