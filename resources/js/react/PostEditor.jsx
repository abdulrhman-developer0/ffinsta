import React, { useEffect, useState } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';
import {
    Bold, Italic, Underline as UnderlineIcon, Strikethrough,
    Heading1, Heading2, Heading3, Heading4,
    AlignLeft, AlignCenter, AlignRight, AlignJustify,
    List, ListOrdered, Quote, ImageIcon, Link as LinkIcon, Unlink, RemoveFormatting, Palette
} from 'lucide-react';

const MenuBar = ({ editor, isRtl }) => {
    if (!editor) {
        return null;
    }

    const addImage = () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = async (e) => {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                const formData = new FormData();
                formData.append('image', file);

                try {
                    const response = await fetch(window.EditorJsUploadRoute || '/admin/posts/upload-media', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.EditorJsCsrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success === 1 && result.file && result.file.url) {
                        editor.chain().focus().setImage({ src: result.file.url }).run();
                    } else {
                        alert('Upload failed');
                    }
                } catch (err) {
                    console.error('Upload error', err);
                }
            }
        };
        input.click();
    };

    const setLink = () => {
        const previousUrl = editor.getAttributes('link').href
        const url = window.prompt('URL', previousUrl)
        if (url === null) return;
        if (url === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run()
            return
        }
        editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
    }

    const Button = ({ onClick, isActive = false, disabled = false, children, title }) => (
        <button
            type="button"
            onClick={onClick}
            disabled={disabled}
            title={title}
            className={`px-2 py-1.5 rounded-sm transition-colors flex items-center justify-center
                ${isActive ? 'bg-slate-200 dark:bg-[#444] text-slate-900 dark:text-white' : 'text-slate-600 dark:text-[#bbb] hover:text-slate-900 hover:bg-slate-200 dark:hover:text-white dark:hover:bg-[#333]'}
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
            `}
        >
            {children}
        </button>
    );

    return (
        <div className="flex flex-col bg-slate-50 dark:bg-[#1a1a1a] border border-slate-200 dark:border-[#333] rounded-t-lg select-none">
            {/* Top Toolbar */}
            <div className="flex flex-wrap items-center gap-1.5 p-1.5 border-b border-slate-200 dark:border-[#2a2a2a]" dir={isRtl ? 'rtl' : 'ltr'}>
                
                {/* Format Clear */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().clearNodes().unsetAllMarks().run()} title="Clear Formatting">
                        <RemoveFormatting size={15} />
                    </Button>
                </div>
                
                {/* Basic Text Formatting */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleBold().run()} isActive={editor.isActive('bold')} title="Bold">
                        <Bold size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleItalic().run()} isActive={editor.isActive('italic')} title="Italic">
                        <Italic size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleUnderline().run()} isActive={editor.isActive('underline')} title="Underline">
                        <UnderlineIcon size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleStrike().run()} isActive={editor.isActive('strike')} title="Strikethrough">
                        <Strikethrough size={15} />
                    </Button>
                </div>

                {/* Headings */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} isActive={editor.isActive('heading', { level: 2 })} title="Heading 2">
                        <Heading2 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()} isActive={editor.isActive('heading', { level: 3 })} title="Heading 3">
                        <Heading3 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 4 }).run()} isActive={editor.isActive('heading', { level: 4 })} title="Heading 4">
                        <Heading4 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setParagraph().run()} isActive={editor.isActive('paragraph')} title="Paragraph">
                        <span className="text-[13px] font-bold leading-none px-0.5">P</span>
                    </Button>
                </div>

                {/* Lists */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleBulletList().run()} isActive={editor.isActive('bulletList')} title="Bullet List">
                        <List size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleOrderedList().run()} isActive={editor.isActive('orderedList')} title="Ordered List">
                        <ListOrdered size={15} />
                    </Button>
                </div>

                {/* Alignment */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().setTextAlign('left').run()} isActive={editor.isActive({ textAlign: 'left' })} title="Align Left">
                        <AlignLeft size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setTextAlign('center').run()} isActive={editor.isActive({ textAlign: 'center' })} title="Align Center">
                        <AlignCenter size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setTextAlign('right').run()} isActive={editor.isActive({ textAlign: 'right' })} title="Align Right">
                        <AlignRight size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setTextAlign('justify').run()} isActive={editor.isActive({ textAlign: 'justify' })} title="Align Justify">
                        <AlignJustify size={15} />
                    </Button>
                </div>

                {/* Colors */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <div className="relative group flex items-center">
                        <Button title="Text Color"><Palette size={15} /></Button>
                        <div className="absolute top-full rtl:right-0 ltr:left-0 mt-1 hidden group-hover:flex flex-wrap w-40 p-2 gap-1 bg-white dark:bg-[#222] shadow-xl rounded-md border border-slate-200 dark:border-[#444] z-50">
                            {['#ffffff', '#1e293b', '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#d946ef'].map(color => (
                                <button key={color} type="button" onClick={() => editor.chain().focus().setColor(color).run()} 
                                        className="w-6 h-6 rounded-full cursor-pointer hover:scale-110 transition-transform shadow-sm border border-slate-200 dark:border-transparent"
                                        style={{ backgroundColor: color }} />
                            ))}
                            <button type="button" onClick={() => editor.chain().focus().unsetColor().run()} className="w-6 h-6 rounded-full cursor-pointer hover:scale-110 transition-transform bg-transparent border border-slate-300 dark:border-[#555] text-slate-600 dark:text-white text-xs flex items-center justify-center font-bold">X</button>
                        </div>
                    </div>
                </div>

                {/* Media & Blockquote */}
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={setLink} isActive={editor.isActive('link')} title="Link">
                        <LinkIcon size={15} />
                    </Button>
                    <Button onClick={addImage} title="Image">
                        <ImageIcon size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleBlockquote().run()} isActive={editor.isActive('blockquote')} title="Blockquote">
                        <Quote size={15} />
                    </Button>
                </div>
            </div>

            {/* Bottom Toolbar (Read more, Code view) */}
            <div className="flex flex-wrap items-center gap-1.5 p-1.5 border-b border-slate-200 dark:border-[#2a2a2a] bg-slate-50 dark:bg-[#1a1a1a]" dir={isRtl ? 'rtl' : 'ltr'}>
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().setHorizontalRule().run()} title="Insert Read more separator">
                        <span className="text-[12px] px-2 font-medium">Read more</span>
                    </Button>
                </div>
                <div className="flex items-center bg-white dark:bg-[#111] border border-slate-200 dark:border-[#333] rounded-md p-0.5 shadow-sm">
                    <Button title="Code View">
                        <span className="text-[12px] px-1 font-mono font-bold">&lt;/&gt;</span>
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default function PostEditor({ initialDataEn, initialDataAr }) {
    const [lang, setLang] = useState('en');

    // Parse existing JSON string to object if necessary
    const parseInitial = (data) => {
        if (!data || data === '{}' || data === '[]') return '';
        try {
            const parsed = typeof data === 'string' ? JSON.parse(data) : data;
            if (Object.keys(parsed).length === 0) return '';
            return parsed;
        } catch (e) {
            return '';
        }
    };

    const parsedEn = parseInitial(initialDataEn);
    const parsedAr = parseInitial(initialDataAr);

    // TipTap editors
    const editorConfig = {
        extensions: [
            StarterKit,
            Underline,
            Image,
            Link.configure({ openOnClick: false }),
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
            TextStyle,
            Color,
        ],
        editorProps: {
            attributes: {
                class: 'prose max-w-none focus:outline-none min-h-[500px] p-6 text-slate-800 dark:text-[#d4d4d4] [&_*]:text-slate-800 dark:[&_*]:text-[#d4d4d4]',
            },
        },
    };

    const editorEn = useEditor({
        ...editorConfig,
        content: parsedEn,
        onUpdate: ({ editor }) => {
            const json = editor.getJSON();
            const input = document.getElementById('content_en');
            if (input) input.value = JSON.stringify(json);
        }
    });

    const editorAr = useEditor({
        ...editorConfig,
        content: parsedAr,
        editorProps: {
            attributes: {
                ...editorConfig.editorProps.attributes,
                dir: 'rtl'
            }
        },
        onUpdate: ({ editor }) => {
            const json = editor.getJSON();
            const input = document.getElementById('content_ar');
            if (input) input.value = JSON.stringify(json);
        }
    });

    useEffect(() => {
        const ensureInput = (id, name) => {
            let input = document.getElementById(id);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.id = id;
                document.getElementById('react-editor-mount')?.parentNode.appendChild(input);
            }
            return input;
        };
        
        const inputEn = ensureInput('content_en', 'content[en]');
        const inputAr = ensureInput('content_ar', 'content[ar]');

        if (editorEn && parsedEn) {
             inputEn.value = JSON.stringify(editorEn.getJSON());
        }
        if (editorAr && parsedAr) {
             inputAr.value = JSON.stringify(editorAr.getJSON());
        }
    }, [editorEn, editorAr]);

    const handleSwitchLang = (newLang) => {
        setLang(newLang);
    };

    return (
        <div className="font-sans">
            <div className="flex justify-between items-center mb-4">
                <div className="flex bg-slate-100 dark:bg-[#1a1a1a] rounded-md p-1 shadow-inner border border-slate-200 dark:border-[#333] w-fit">
                    <button 
                        type="button" 
                        onClick={() => handleSwitchLang('en')}
                        className={`px-5 py-1.5 text-sm font-bold rounded transition-all flex items-center gap-2 ${
                            lang === 'en' 
                            ? 'bg-white text-slate-800 border-slate-300 dark:bg-[#333] shadow-sm dark:text-white border dark:border-[#444]' 
                            : 'text-slate-500 dark:text-[#888] hover:text-slate-800 dark:hover:text-white'
                        }`}
                    >
                        <span>EN</span>
                    </button>
                    <button 
                        type="button" 
                        onClick={() => handleSwitchLang('ar')}
                        className={`px-5 py-1.5 text-sm font-bold rounded transition-all flex items-center gap-2 ${
                            lang === 'ar' 
                            ? 'bg-white text-slate-800 border-slate-300 dark:bg-[#333] shadow-sm dark:text-white border dark:border-[#444]' 
                            : 'text-slate-500 dark:text-[#888] hover:text-slate-800 dark:hover:text-white'
                        }`}
                    >
                        <span>AR</span>
                    </button>
                </div>
            </div>

            <div style={{ display: lang === 'en' ? 'block' : 'none' }} className="border border-slate-200 dark:border-[#333] rounded-lg shadow-sm overflow-hidden bg-white dark:bg-[#262626]">
                <MenuBar editor={editorEn} isRtl={false} />
                <div className="max-h-[700px] overflow-y-auto bg-white dark:bg-[#2b2b2b]">
                    <EditorContent editor={editorEn} />
                </div>
            </div>
            
            <div style={{ display: lang === 'ar' ? 'block' : 'none' }} className="border border-slate-200 dark:border-[#333] rounded-lg shadow-sm overflow-hidden bg-white dark:bg-[#262626]">
                <MenuBar editor={editorAr} isRtl={true} />
                <div className="max-h-[700px] overflow-y-auto bg-white dark:bg-[#2b2b2b]" dir="rtl">
                    <EditorContent editor={editorAr} />
                </div>
            </div>
        </div>
    );
}
