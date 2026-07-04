import React, { useEffect, useState } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import TextStyle from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
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

        if (url === null) {
            return
        }

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
            className={`p-1.5 rounded-lg transition-colors flex items-center justify-center
                ${isActive ? 'bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800'}
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
            `}
        >
            {children}
        </button>
    );

    const Divider = () => <div className="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1" />;

    return (
        <div className="flex flex-wrap items-center gap-1 p-2 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 rounded-t-2xl z-10 sticky top-0" dir={isRtl ? 'rtl' : 'ltr'}>
            <Button onClick={() => editor.chain().focus().toggleBold().run()} disabled={!editor.can().chain().focus().toggleBold().run()} isActive={editor.isActive('bold')} title="Bold">
                <Bold size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleItalic().run()} disabled={!editor.can().chain().focus().toggleItalic().run()} isActive={editor.isActive('italic')} title="Italic">
                <Italic size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleUnderline().run()} disabled={!editor.can().chain().focus().toggleUnderline().run()} isActive={editor.isActive('underline')} title="Underline">
                <UnderlineIcon size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleStrike().run()} disabled={!editor.can().chain().focus().toggleStrike().run()} isActive={editor.isActive('strike')} title="Strikethrough">
                <Strikethrough size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().clearNodes().unsetAllMarks().run()} title="Clear Formatting">
                <RemoveFormatting size={18} />
            </Button>
            
            <Divider />
            
            <Button onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} isActive={editor.isActive('heading', { level: 2 })} title="Heading 1 (H2)">
                <Heading1 size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()} isActive={editor.isActive('heading', { level: 3 })} title="Heading 2 (H3)">
                <Heading2 size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleHeading({ level: 4 }).run()} isActive={editor.isActive('heading', { level: 4 })} title="Heading 3 (H4)">
                <Heading3 size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().setParagraph().run()} isActive={editor.isActive('paragraph')} title="Paragraph">
                <span className="font-bold px-1 text-sm">P</span>
            </Button>
            
            <Divider />
            
            <Button onClick={() => editor.chain().focus().setTextAlign('left').run()} isActive={editor.isActive({ textAlign: 'left' })} title="Align Left">
                <AlignLeft size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().setTextAlign('center').run()} isActive={editor.isActive({ textAlign: 'center' })} title="Align Center">
                <AlignCenter size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().setTextAlign('right').run()} isActive={editor.isActive({ textAlign: 'right' })} title="Align Right">
                <AlignRight size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().setTextAlign('justify').run()} isActive={editor.isActive({ textAlign: 'justify' })} title="Align Justify">
                <AlignJustify size={18} />
            </Button>

            <Divider />
            
            <Button onClick={() => editor.chain().focus().toggleBulletList().run()} isActive={editor.isActive('bulletList')} title="Bullet List">
                <List size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleOrderedList().run()} isActive={editor.isActive('orderedList')} title="Ordered List">
                <ListOrdered size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().toggleBlockquote().run()} isActive={editor.isActive('blockquote')} title="Blockquote">
                <Quote size={18} />
            </Button>
            
            <Divider />
            
            <Button onClick={addImage} title="Image">
                <ImageIcon size={18} />
            </Button>
            <Button onClick={setLink} isActive={editor.isActive('link')} title="Link">
                <LinkIcon size={18} />
            </Button>
            <Button onClick={() => editor.chain().focus().unsetLink().run()} disabled={!editor.isActive('link')} title="Unlink">
                <Unlink size={18} />
            </Button>
            
            <Divider />
            
            <div className="relative group flex items-center">
                <Button title="Text Color"><Palette size={18} /></Button>
                <div className="absolute top-full rtl:right-0 ltr:left-0 mt-1 hidden group-hover:flex flex-wrap w-40 p-2 gap-1 bg-white dark:bg-slate-800 shadow-xl rounded-xl border border-slate-200 dark:border-slate-700">
                    {['#1e293b', '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#d946ef'].map(color => (
                        <button key={color} type="button" onClick={() => editor.chain().focus().setColor(color).run()} 
                                className="w-6 h-6 rounded-full cursor-pointer hover:scale-110 transition-transform"
                                style={{ backgroundColor: color }} />
                    ))}
                    <button type="button" onClick={() => editor.chain().focus().unsetColor().run()} className="w-6 h-6 rounded-full cursor-pointer hover:scale-110 transition-transform bg-transparent border-2 border-slate-300 text-xs flex items-center justify-center font-bold">X</button>
                </div>
            </div>
        </div>
    );
};

export default function PostEditor({ initialDataEn, initialDataAr }) {
    const [lang, setLang] = useState('en');

    // Parse existing JSON string to object if necessary
    const parseInitial = (data) => {
        if (!data) return {};
        try {
            return typeof data === 'string' ? JSON.parse(data) : data;
        } catch (e) {
            return {};
        }
    };

    const parsedEn = parseInitial(initialDataEn);
    const parsedAr = parseInitial(initialDataAr);

    // TipTap editors
    const editorEn = useEditor({
        extensions: [
            StarterKit,
            Underline,
            Image,
            Link.configure({ openOnClick: false }),
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
            TextStyle,
            Color,
        ],
        content: parsedEn,
        editorProps: {
            attributes: {
                class: 'prose prose-slate dark:prose-invert max-w-none focus:outline-none min-h-[500px] p-6',
            },
        },
        onUpdate: ({ editor }) => {
            const json = editor.getJSON();
            const input = document.getElementById('content_en');
            if (input) input.value = JSON.stringify(json);
        }
    });

    const editorAr = useEditor({
        extensions: [
            StarterKit,
            Underline,
            Image,
            Link.configure({ openOnClick: false }),
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
            TextStyle,
            Color,
        ],
        content: parsedAr,
        editorProps: {
            attributes: {
                class: 'prose prose-slate dark:prose-invert max-w-none focus:outline-none min-h-[500px] p-6',
                dir: 'rtl'
            },
        },
        onUpdate: ({ editor }) => {
            const json = editor.getJSON();
            const input = document.getElementById('content_ar');
            if (input) input.value = JSON.stringify(json);
        }
    });

    useEffect(() => {
        // Ensure hidden inputs exist
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

        if (editorEn && Object.keys(parsedEn).length > 0) {
             inputEn.value = JSON.stringify(editorEn.getJSON());
        }
        if (editorAr && Object.keys(parsedAr).length > 0) {
             inputAr.value = JSON.stringify(editorAr.getJSON());
        }
    }, [editorEn, editorAr]);

    const handleSwitchLang = (newLang) => {
        setLang(newLang);
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

            <div style={{ display: lang === 'en' ? 'block' : 'none' }} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm overflow-hidden relative">
                <MenuBar editor={editorEn} isRtl={false} />
                <div className="max-h-[700px] overflow-y-auto">
                    <EditorContent editor={editorEn} />
                </div>
            </div>
            
            <div style={{ display: lang === 'ar' ? 'block' : 'none' }} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-sm overflow-hidden relative">
                <MenuBar editor={editorAr} isRtl={true} />
                <div className="max-h-[700px] overflow-y-auto" dir="rtl">
                    <EditorContent editor={editorAr} />
                </div>
            </div>
        </div>
    );
}
