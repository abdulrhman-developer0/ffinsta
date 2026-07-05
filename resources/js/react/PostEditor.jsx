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
                ${isActive ? 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 hover:bg-slate-200 dark:hover:text-white dark:hover:bg-slate-600'}
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
            `}
        >
            {children}
        </button>
    );

    return (
        <div className="flex flex-col bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-t-lg select-none">
            {/* Top Toolbar */}
            <div className="flex flex-wrap items-center gap-1.5 p-1.5 border-b border-slate-200 dark:border-slate-700/80" dir={isRtl ? 'rtl' : 'ltr'}>
                
                {/* Format Clear */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().clearNodes().unsetAllMarks().run()} title="Clear Formatting">
                        <RemoveFormatting size={15} />
                    </Button>
                </div>
                
                {/* Basic Text Formatting */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
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
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
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
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleBulletList().run()} isActive={editor.isActive('bulletList')} title="Bullet List">
                        <List size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleOrderedList().run()} isActive={editor.isActive('orderedList')} title="Ordered List">
                        <ListOrdered size={15} />
                    </Button>
                </div>

                {/* Alignment */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
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
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <label title="Text Color" className="cursor-pointer p-1.5 rounded-md flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors relative overflow-hidden">
                        <Palette size={15} />
                        <input 
                            type="color" 
                            className="absolute top-0 left-0 opacity-0 w-full h-full cursor-pointer"
                            onChange={(e) => editor.chain().focus().setColor(e.target.value).run()}
                        />
                    </label>
                    <Button onClick={() => editor.chain().focus().unsetColor().run()} title="Remove Color">
                        <RemoveFormatting size={14} />
                    </Button>
                </div>

                {/* Media & Blockquote */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
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
            <div className="flex flex-wrap items-center gap-1.5 p-1.5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800" dir={isRtl ? 'rtl' : 'ltr'}>
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleCodeBlock().run()} isActive={editor.isActive('codeBlock')} title="Code Block">
                        <span className="text-[12px] px-1 font-mono font-bold">&lt;/&gt;</span>
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default function PostEditor({ initialDataEn, initialDataAr }) {
    const [lang, setLang] = useState('en');

    const parseInitial = (data) => {
        if (!data || data === '{}' || data === '[]') return '';
        if (typeof data === 'string' && data.trim().startsWith('<')) return data; // Raw HTML
        try {
            const parsed = typeof data === 'string' ? JSON.parse(data) : data;
            if (Object.keys(parsed).length === 0) return '';
            return parsed;
        } catch (e) {
            return typeof data === 'string' ? data : ''; // Return raw string if JSON parsing fails
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
                class: 'prose dark:prose-invert max-w-none focus:outline-none p-6 text-slate-800 dark:text-slate-200 [&_*]:text-slate-800 dark:[&_*]:text-slate-200',
                style: 'min-height: 500px;',
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
                <div className="flex bg-slate-100 dark:bg-slate-800 rounded-md p-1 shadow-inner border border-slate-200 dark:border-slate-700 w-fit">
                    <button 
                        type="button" 
                        onClick={() => handleSwitchLang('en')}
                        className={`px-5 py-1.5 text-sm font-bold rounded transition-all flex items-center gap-2 ${
                            lang === 'en' 
                            ? 'bg-white text-slate-800 border-slate-300 dark:bg-slate-700 shadow-sm dark:text-white border dark:border-slate-600' 
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'
                        }`}
                    >
                        <span>EN</span>
                    </button>
                    <button 
                        type="button" 
                        onClick={() => handleSwitchLang('ar')}
                        className={`px-5 py-1.5 text-sm font-bold rounded transition-all flex items-center gap-2 ${
                            lang === 'ar' 
                            ? 'bg-white text-slate-800 border-slate-300 dark:bg-slate-700 shadow-sm dark:text-white border dark:border-slate-600' 
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'
                        }`}
                    >
                        <span>AR</span>
                    </button>
                </div>
            </div>

            <div style={{ display: lang === 'en' ? 'block' : 'none' }} className="border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm overflow-hidden bg-white dark:bg-slate-800">
                <MenuBar editor={editorEn} isRtl={false} />
                <div className="max-h-[700px] overflow-y-auto bg-transparent">
                    <EditorContent editor={editorEn} className="bg-transparent dark:bg-transparent" />
                </div>
            </div>
            
            <div style={{ display: lang === 'ar' ? 'block' : 'none' }} className="border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm overflow-hidden bg-white dark:bg-slate-800">
                <MenuBar editor={editorAr} isRtl={true} />
                <div className="max-h-[700px] overflow-y-auto bg-transparent" dir="rtl">
                    <EditorContent editor={editorAr} className="bg-transparent dark:bg-transparent" />
                </div>
            </div>
        </div>
    );
}
