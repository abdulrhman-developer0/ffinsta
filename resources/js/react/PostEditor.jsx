import React, { useEffect, useState, useRef } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';
import {
    Bold, Italic, Strikethrough, Underline as UnderlineIcon,
    Heading1, Heading2, Heading3, Heading4, Heading5,
    AlignLeft, AlignCenter, AlignRight, AlignJustify,
    List, ListOrdered, Quote, ImageIcon, Link as LinkIcon, Unlink, RemoveFormatting, Palette, Code
} from 'lucide-react';

const __ = (key) => {
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    const dict = {
        'Text Color': 'لون النص',
        'Custom Color': 'لون مخصص',
        'Remove Color': 'إزالة اللون',
        'Link': 'رابط',
        'Save': 'حفظ',
        'Clear Formatting': 'مسح التنسيق',
        'Bold': 'عريض',
        'Italic': 'مائل',
        'Underline': 'تسطير',
        'Strikethrough': 'يتوسطه خط',
        'Heading 1': 'ترويسة 1',
        'Heading 2': 'ترويسة 2',
        'Heading 3': 'ترويسة 3',
        'Heading 4': 'ترويسة 4',
        'Heading 5': 'ترويسة 5',
        'Paragraph': 'فقرة',
        'Bullet List': 'قائمة نقطية',
        'Ordered List': 'قائمة رقمية',
        'Align Left': 'محاذاة لليسار',
        'Align Center': 'توسيط',
        'Align Right': 'محاذاة لليمين',
        'Align Justify': 'ضبط المحاذاة',
        'Image': 'صورة',
        'Blockquote': 'اقتباس',
        'Code Block': 'كتلة كود',
        'HTML Source Code': 'كود HTML المصدر'
    };
    return isAr ? (dict[key] || key) : key;
};

const ColorPickerDropdown = ({ editor, isRtl, isHtmlMode }) => {
    const [isOpen, setIsOpen] = useState(false);
    const popoverRef = useRef(null);

    const colors = [
        '#000000', '#475569', '#ef4444', '#f97316', '#f59e0b', 
        '#84cc16', '#22c55e', '#06b6d4', '#3b82f6', '#6366f1', 
        '#d946ef', '#f43f5e', '#ffffff'
    ];

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (popoverRef.current && !popoverRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const [dropdownStyle, setDropdownStyle] = useState({});

    useEffect(() => {
        if (isOpen && popoverRef.current) {
            const rect = popoverRef.current.getBoundingClientRect();
            const spaceRight = window.innerWidth - rect.right;
            const spaceLeft = rect.left;
            
            const isDocRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
            
            if (isDocRtl) {
                if (rect.right < 200) {
                    setDropdownStyle({ left: 0, right: 'auto' });
                } else {
                    setDropdownStyle({ right: 0, left: 'auto' });
                }
            } else {
                if (spaceRight < 200) {
                    setDropdownStyle({ right: 0, left: 'auto' });
                } else {
                    setDropdownStyle({ left: 0, right: 'auto' });
                }
            }
        }
    }, [isOpen]);

    const currentColor = editor.getAttributes('textStyle').color || '#000000';

    return (
        <div className="relative" ref={popoverRef}>
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                title={__('Text Color')}
                disabled={isHtmlMode}
                className={`p-1.5 rounded-md flex items-center justify-center transition-colors ${
                    isOpen 
                        ? 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white' 
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'
                } ${isHtmlMode ? 'opacity-50 cursor-not-allowed' : ''}`}
            >
                <Palette size={15} color={editor.getAttributes('textStyle').color || 'currentColor'} />
            </button>

            {isOpen && (
                <div 
                    className="absolute top-full mt-1 w-48 p-3 bg-white dark:bg-slate-800 shadow-xl rounded-lg border border-slate-200 dark:border-slate-700 z-50 flex flex-col gap-3"
                    style={dropdownStyle}
                >
                    <div className="flex flex-wrap gap-1.5">
                        {colors.map(color => (
                            <button
                                key={color}
                                type="button"
                                onClick={() => {
                                    editor.chain().focus().setColor(color).run();
                                    setIsOpen(false);
                                }}
                                className={`w-6 h-6 rounded-full cursor-pointer hover:scale-110 transition-transform shadow-sm border ${currentColor === color ? 'border-blue-500 scale-110' : 'border-slate-200 dark:border-slate-600'}`}
                                style={{ backgroundColor: color }}
                                title={color}
                            />
                        ))}
                    </div>

                    <div className="flex items-center gap-2">
                        <input 
                            type="color" 
                            value={currentColor} 
                            onChange={(e) => editor.chain().focus().setColor(e.target.value).run()}
                            className="w-8 h-8 p-0 border border-slate-200 dark:border-slate-600 rounded cursor-pointer shrink-0"
                            title={__('Custom Color')}
                        />
                        <input 
                            type="text" 
                            value={currentColor}
                            onChange={(e) => editor.chain().focus().setColor(e.target.value).run()}
                            className="flex-1 px-2 py-1 text-xs border border-slate-300 dark:border-slate-600 rounded bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 uppercase w-full"
                            placeholder="#HEX"
                            dir="ltr"
                        />
                    </div>

                    <button 
                        type="button" 
                        onClick={() => {
                            editor.chain().focus().unsetColor().run();
                            setIsOpen(false);
                        }}
                        className="w-full py-1.5 mt-1 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 rounded transition-colors flex items-center justify-center gap-1"
                    >
                        <RemoveFormatting size={14} /> {__('Remove Color')}
                    </button>
                </div>
            )}
        </div>
    );
};

const LinkDropdown = ({ editor, isHtmlMode }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [url, setUrl] = useState('');
    const popoverRef = useRef(null);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (popoverRef.current && !popoverRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const openDropdown = () => {
        const previousUrl = editor.getAttributes('link').href || '';
        setUrl(previousUrl);
        setIsOpen(!isOpen);
    };

    const handleSave = () => {
        if (url === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
        } else {
            editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        }
        setIsOpen(false);
    };

    const handleRemove = () => {
        editor.chain().focus().extendMarkRange('link').unsetLink().run();
        setIsOpen(false);
    };

    return (
        <div className="relative" ref={popoverRef}>
            <button
                type="button"
                onClick={openDropdown}
                title={__('Link')}
                disabled={isHtmlMode}
                className={`p-1.5 rounded-md flex items-center justify-center transition-colors ${
                    editor.isActive('link') || isOpen
                        ? 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white' 
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'
                } ${isHtmlMode ? 'opacity-50 cursor-not-allowed' : ''}`}
            >
                <LinkIcon size={15} />
            </button>

            {isOpen && (
                <div className="absolute top-full mt-1 w-64 p-3 bg-white dark:bg-slate-800 shadow-xl rounded-lg border border-slate-200 dark:border-slate-700 z-50 flex flex-col gap-2 ltr:left-0 rtl:right-0">
                    <div className="flex items-center gap-2">
                        <input 
                            type="url" 
                            value={url}
                            onChange={(e) => setUrl(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && handleSave()}
                            className="flex-1 px-2 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200"
                            placeholder="https://example.com"
                            dir="ltr"
                            autoFocus
                        />
                    </div>
                    <div className="flex gap-2 mt-1">
                        <button 
                            type="button" 
                            onClick={handleSave}
                            className="flex-1 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors"
                        >
                            {__('Save')}
                        </button>
                        {editor.isActive('link') && (
                            <button 
                                type="button" 
                                onClick={handleRemove}
                                className="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 rounded transition-colors"
                            >
                                <Unlink size={14} />
                            </button>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};

const formatHTML = (html) => {
    let formatted = '';
    let indent = 0;
    const tab = '    ';
    html.split(/>\s*</).forEach(function(element) {
        if (element.match(/^\/\w/)) {
            indent -= 1;
        }
        formatted += tab.repeat(Math.max(0, indent)) + '<' + element + '>\n';
        if (element.match(/^<?\w[^>]*[^\/]$/) && !element.startsWith("input") && !element.startsWith("img") && !element.startsWith("br")) {
            indent += 1;
        }
    });
    return formatted.substring(1, formatted.length - 2); // trim extra < >
};

const CodeEditor = ({ value, onChange, isRtl }) => {
    const lineCount = value.split('\n').length;
    const lines = Array.from({ length: Math.max(lineCount, 1) }, (_, i) => i + 1);
    
    // Calculate height based on lines. 24px per line + 48px padding (py-6 is 24px top and bottom).
    const calculatedHeight = Math.max(lineCount * 24 + 48, 500);

    return (
        <div 
            className="relative flex w-full bg-slate-50 dark:bg-slate-900/40 font-mono text-sm leading-relaxed" 
            dir="ltr"
            style={{ height: `${calculatedHeight}px` }}
        >
            <div 
                className="py-6 px-4 text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/50 text-right select-none border-r border-slate-200 dark:border-slate-700/80 shrink-0"
            >
                {lines.map(line => <div key={line} className="h-[24px] leading-[24px]">{line}</div>)}
            </div>
            <textarea 
                className="flex-1 w-full h-full p-6 bg-transparent text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-0 resize-none whitespace-pre font-mono leading-[24px] overflow-x-auto overflow-y-hidden"
                value={value}
                onChange={onChange}
                dir="ltr"
                spellCheck="false"
                wrap="off"
            />
        </div>
    );
};

const MenuBar = ({ editor, isRtl, isHtmlMode, onToggleHtml }) => {
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
            <div className="flex flex-wrap items-center gap-1.5 p-1.5 border-b border-slate-200 dark:border-slate-700/80">
                
                {/* Format Clear */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().clearNodes().unsetAllMarks().run()} title={__('Clear Formatting')} disabled={isHtmlMode}>
                        <RemoveFormatting size={15} />
                    </Button>
                </div>
                
                {/* Basic Text Formatting */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleBold().run()} isActive={editor.isActive('bold')} title={__('Bold')} disabled={isHtmlMode}>
                        <Bold size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleItalic().run()} isActive={editor.isActive('italic')} title={__('Italic')} disabled={isHtmlMode}>
                        <Italic size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleUnderline().run()} isActive={editor.isActive('underline')} title={__('Underline')} disabled={isHtmlMode}>
                        <UnderlineIcon size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleStrike().run()} isActive={editor.isActive('strike')} title={__('Strikethrough')} disabled={isHtmlMode}>
                        <Strikethrough size={15} />
                    </Button>
                </div>

                {/* Headings */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} isActive={editor.isActive('heading', { level: 2 })} title={__('Heading 1')} disabled={isHtmlMode}>
                        <Heading1 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()} isActive={editor.isActive('heading', { level: 3 })} title={__('Heading 2')} disabled={isHtmlMode}>
                        <Heading2 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 4 }).run()} isActive={editor.isActive('heading', { level: 4 })} title={__('Heading 3')} disabled={isHtmlMode}>
                        <Heading3 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 5 }).run()} isActive={editor.isActive('heading', { level: 5 })} title={__('Heading 4')} disabled={isHtmlMode}>
                        <Heading4 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleHeading({ level: 6 }).run()} isActive={editor.isActive('heading', { level: 6 })} title={__('Heading 5')} disabled={isHtmlMode}>
                        <Heading5 size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setParagraph().run()} isActive={editor.isActive('paragraph')} title={__('Paragraph')} disabled={isHtmlMode}>
                        <span className="text-[13px] font-bold leading-none px-0.5">P</span>
                    </Button>
                </div>

                {/* Lists */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().toggleBulletList().run()} isActive={editor.isActive('bulletList')} title={__('Bullet List')} disabled={isHtmlMode}>
                        <List size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleOrderedList().run()} isActive={editor.isActive('orderedList')} title={__('Ordered List')} disabled={isHtmlMode}>
                        <ListOrdered size={15} />
                    </Button>
                </div>

                {/* Alignment */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm">
                    <Button onClick={() => editor.chain().focus().setTextAlign('left').run()} isActive={editor.isActive({ textAlign: 'left' })} title={__('Align Left')} disabled={isHtmlMode}>
                        <AlignLeft size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setTextAlign('center').run()} isActive={editor.isActive({ textAlign: 'center' })} title={__('Align Center')} disabled={isHtmlMode}>
                        <AlignCenter size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setTextAlign('right').run()} isActive={editor.isActive({ textAlign: 'right' })} title={__('Align Right')} disabled={isHtmlMode}>
                        <AlignRight size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().setTextAlign('justify').run()} isActive={editor.isActive({ textAlign: 'justify' })} title={__('Align Justify')} disabled={isHtmlMode}>
                        <AlignJustify size={15} />
                    </Button>
                </div>

                {/* Colors */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm z-50">
                    <ColorPickerDropdown editor={editor} isRtl={isRtl} isHtmlMode={isHtmlMode} />
                </div>

                {/* Media, Blockquote & Code */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm z-50">
                    <LinkDropdown editor={editor} isHtmlMode={isHtmlMode} />
                    <Button onClick={addImage} title={__('Image')} disabled={isHtmlMode}>
                        <ImageIcon size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleBlockquote().run()} isActive={editor.isActive('blockquote')} title={__('Blockquote')} disabled={isHtmlMode}>
                        <Quote size={15} />
                    </Button>
                    <Button onClick={() => editor.chain().focus().toggleCodeBlock().run()} isActive={editor.isActive('codeBlock')} title={__('Code Block')} disabled={isHtmlMode}>
                        <span className="text-[12px] px-1 font-mono font-bold">&lt;/&gt;</span>
                    </Button>
                </div>

                {/* HTML Source Mode Toggle */}
                <div className="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-md p-0.5 shadow-sm ms-auto">
                    <Button onClick={onToggleHtml} isActive={isHtmlMode} title={__('HTML Source Code')}>
                        <Code size={15} />
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default function PostEditor({ initialDataEn, initialDataAr }) {
    const [lang, setLang] = useState('en');
    const [htmlMode, setHtmlMode] = useState({ en: false, ar: false });
    const [htmlContent, setHtmlContent] = useState({ en: '', ar: '' });

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
                class: 'prose dark:prose-invert max-w-none focus:outline-none p-6 text-slate-800 dark:text-slate-200 [&_*]:text-slate-800 dark:[&_*]:text-slate-200 prose-h2:text-3xl prose-h3:text-2xl prose-h4:text-xl prose-h5:text-lg prose-h6:text-base prose-headings:font-bold',
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
        const form = document.querySelector('form');
        if (!form) return;

        const handleFormSubmit = () => {
            if (htmlMode.en && editorEn) {
                editorEn.commands.setContent(htmlContent.en);
                const inputEn = document.getElementById('content_en');
                if (inputEn) inputEn.value = JSON.stringify(editorEn.getJSON());
            }
            if (htmlMode.ar && editorAr) {
                editorAr.commands.setContent(htmlContent.ar);
                const inputAr = document.getElementById('content_ar');
                if (inputAr) inputAr.value = JSON.stringify(editorAr.getJSON());
            }
        };

        form.addEventListener('submit', handleFormSubmit);
        return () => form.removeEventListener('submit', handleFormSubmit);
    }, [htmlMode, htmlContent, editorEn, editorAr]);

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

        if (editorEn && parsedEn && !htmlMode.en) {
             inputEn.value = JSON.stringify(editorEn.getJSON());
        }
        if (editorAr && parsedAr && !htmlMode.ar) {
             inputAr.value = JSON.stringify(editorAr.getJSON());
        }
    }, [editorEn, editorAr, htmlMode]);

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

            <div style={{ display: lang === 'en' ? 'block' : 'none' }} className="border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm overflow-hidden bg-white dark:bg-slate-800 flex flex-col">
                <MenuBar 
                    editor={editorEn} 
                    isRtl={false} 
                    isHtmlMode={htmlMode.en}
                    onToggleHtml={() => {
                        if (!htmlMode.en) {
                            setHtmlContent(prev => ({...prev, en: formatHTML(editorEn.getHTML())}));
                        } else {
                            editorEn.commands.setContent(htmlContent.en);
                            const inputEn = document.getElementById('content_en');
                            if (inputEn) inputEn.value = JSON.stringify(editorEn.getJSON());
                        }
                        setHtmlMode(prev => ({...prev, en: !prev.en}));
                    }}
                />
                <div className="flex-1 max-h-[700px] overflow-y-auto bg-transparent relative border-t border-slate-200 dark:border-slate-700/80">
                    {htmlMode.en ? (
                        <CodeEditor 
                            value={htmlContent.en}
                            onChange={(e) => setHtmlContent(prev => ({...prev, en: e.target.value}))}
                            isRtl={false}
                        />
                    ) : (
                        <EditorContent editor={editorEn} className="bg-transparent dark:bg-transparent min-h-[500px]" />
                    )}
                </div>
            </div>
            
            <div style={{ display: lang === 'ar' ? 'block' : 'none' }} className="border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm overflow-hidden bg-white dark:bg-slate-800 flex flex-col">
                <MenuBar 
                    editor={editorAr} 
                    isRtl={true} 
                    isHtmlMode={htmlMode.ar}
                    onToggleHtml={() => {
                        if (!htmlMode.ar) {
                            setHtmlContent(prev => ({...prev, ar: formatHTML(editorAr.getHTML())}));
                        } else {
                            editorAr.commands.setContent(htmlContent.ar);
                            const inputAr = document.getElementById('content_ar');
                            if (inputAr) inputAr.value = JSON.stringify(editorAr.getJSON());
                        }
                        setHtmlMode(prev => ({...prev, ar: !prev.ar}));
                    }}
                />
                <div className="flex-1 max-h-[700px] overflow-y-auto bg-transparent relative border-t border-slate-200 dark:border-slate-700/80" dir={htmlMode.ar ? "ltr" : "rtl"}>
                    {htmlMode.ar ? (
                        <CodeEditor 
                            value={htmlContent.ar}
                            onChange={(e) => setHtmlContent(prev => ({...prev, ar: e.target.value}))}
                            isRtl={true}
                        />
                    ) : (
                        <EditorContent editor={editorAr} className="bg-transparent dark:bg-transparent min-h-[500px]" />
                    )}
                </div>
            </div>
        </div>
    );
}
