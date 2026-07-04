<?php

namespace App\Services;

class PostRenderService
{
    /**
     * Splits unified JSON content back into a standard structure for a specific locale.
     * With TipTap, the DB might store `{ "en": { type: "doc", ... }, "ar": { type: "doc", ... } }`.
     */
    public function getLocalizedContent($unifiedContent, $locale)
    {
        if (!is_array($unifiedContent)) {
            return $unifiedContent;
        }

        // Check if it's already localized (e.g. ['en' => [...], 'ar' => [...]])
        if (isset($unifiedContent['en']) || isset($unifiedContent['ar'])) {
            return $unifiedContent[$locale] ?? null;
        }

        // If it's old EditorJS blocks or something else without en/ar wrapper
        return $unifiedContent;
    }

    /**
     * Extracts plain text from TipTap JSON content.
     */
    public function getPlainText($unifiedContent, $locale)
    {
        $contentData = $this->getLocalizedContent($unifiedContent, $locale);
        
        if (is_string($contentData)) {
            return strip_tags($contentData);
        }

        return $this->extractTextFromNode($contentData);
    }

    private function extractTextFromNode($node)
    {
        if (!is_array($node)) return '';
        
        $text = '';
        if (isset($node['type']) && $node['type'] === 'text' && isset($node['text'])) {
            $text .= $node['text'];
        }
        
        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                $text .= $this->extractTextFromNode($child) . ' ';
            }
        }
        
        return trim($text);
    }

    /**
     * Renders TipTap JSON content into HTML.
     */
    public function renderHtml($unifiedContent, $locale)
    {
        $contentData = $this->getLocalizedContent($unifiedContent, $locale);
        
        if (is_string($contentData)) {
            return $contentData; // Fallback for old HTML
        }
        
        if (!is_array($contentData) || !isset($contentData['type']) || $contentData['type'] !== 'doc') {
            return ''; // Invalid format or empty
        }

        return $this->renderNode($contentData);
    }

    private function renderNode($node)
    {
        if (!is_array($node) || !isset($node['type'])) {
            return '';
        }

        $type = $node['type'];
        $contentHtml = '';
        
        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                $contentHtml .= $this->renderNode($child);
            }
        }

        $attrs = $node['attrs'] ?? [];
        $textAlign = $attrs['textAlign'] ?? null;
        $class = '';
        if ($textAlign && in_array($textAlign, ['left', 'center', 'right', 'justify'])) {
            $class = "text-{$textAlign}";
        }
        $classAttr = $class ? " class=\"{$class}\"" : '';

        switch ($type) {
            case 'doc':
                return $contentHtml;
            case 'paragraph':
                if (empty(trim(strip_tags($contentHtml))) && empty($node['content'])) {
                    return "<p{$classAttr}><br></p>";
                }
                return "<p{$classAttr}>{$contentHtml}</p>";
            case 'heading':
                $level = $attrs['level'] ?? 2;
                return "<h{$level}{$classAttr}>{$contentHtml}</h{$level}>";
            case 'blockquote':
                return "<blockquote{$classAttr}>{$contentHtml}</blockquote>";
            case 'bulletList':
                return "<ul{$classAttr}>{$contentHtml}</ul>";
            case 'orderedList':
                return "<ol{$classAttr}>{$contentHtml}</ol>";
            case 'listItem':
                return "<li>{$contentHtml}</li>";
            case 'image':
                $src = $attrs['src'] ?? '';
                $alt = $attrs['alt'] ?? '';
                $title = $attrs['title'] ?? '';
                $imgClass = "max-w-full h-auto rounded-xl shadow-lg my-6 mx-auto";
                if ($class === 'text-left') $imgClass .= " ml-0 mr-auto";
                elseif ($class === 'text-right') $imgClass .= " ml-auto mr-0";
                
                if ($src) {
                    return "<figure class=\"{$class}\"><img src=\"{$src}\" alt=\"{$alt}\" title=\"{$title}\" class=\"{$imgClass}\"></figure>";
                }
                return '';
            case 'text':
                $text = htmlspecialchars($node['text'] ?? '');
                if (isset($node['marks']) && is_array($node['marks'])) {
                    foreach ($node['marks'] as $mark) {
                        $text = $this->applyMark($text, $mark);
                    }
                }
                return $text;
            case 'hardBreak':
                return "<br>";
            case 'horizontalRule':
                return "<hr class=\"my-8 border-t-2 border-slate-200 dark:border-slate-700\">";
            default:
                return $contentHtml; // Unhandled node, just render its content
        }
    }

    private function applyMark($text, $mark)
    {
        if (!is_array($mark) || !isset($mark['type'])) {
            return $text;
        }

        $type = $mark['type'];
        $attrs = $mark['attrs'] ?? [];

        switch ($type) {
            case 'bold':
                return "<strong>{$text}</strong>";
            case 'italic':
                return "<em>{$text}</em>";
            case 'underline':
                return "<u>{$text}</u>";
            case 'strike':
                return "<s>{$text}</s>";
            case 'link':
                $href = $attrs['href'] ?? '#';
                $target = $attrs['target'] ?? '_blank';
                return "<a href=\"{$href}\" target=\"{$target}\" rel=\"noopener noreferrer\" class=\"text-brand-600 hover:text-brand-700 underline\">{$text}</a>";
            case 'textStyle':
                $color = $attrs['color'] ?? null;
                if ($color) {
                    return "<span style=\"color: {$color}\">{$text}</span>";
                }
                return $text;
            default:
                return $text;
        }
    }
}
