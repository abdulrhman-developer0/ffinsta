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
        $styleAttr = '';
        if ($textAlign && in_array($textAlign, ['left', 'center', 'right', 'justify'])) {
            $styleAttr = " style=\"text-align: {$textAlign};\"";
        }

        switch ($type) {
            case 'doc':
                return $contentHtml;
            case 'paragraph':
                if (empty(trim(strip_tags($contentHtml))) && empty($node['content'])) {
                    return "<p{$styleAttr}><br></p>";
                }
                return "<p{$styleAttr}>{$contentHtml}</p>";
            case 'heading':
                $level = $attrs['level'] ?? 2;
                return "<h{$level}{$styleAttr}>{$contentHtml}</h{$level}>";
            case 'blockquote':
                return "<blockquote{$styleAttr}>{$contentHtml}</blockquote>";
            case 'codeBlock':
                return "<pre><code{$styleAttr}>{$contentHtml}</code></pre>";
            case 'bulletList':
                return "<ul{$styleAttr}>{$contentHtml}</ul>";
            case 'orderedList':
                return "<ol{$styleAttr}>{$contentHtml}</ol>";
            case 'listItem':
                return "<li>{$contentHtml}</li>";
            case 'image':
                $src = $attrs['src'] ?? '';
                $alt = $attrs['alt'] ?? '';
                $title = $attrs['title'] ?? '';
                $imgStyle = "max-width: 100%; height: auto; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); margin: 1.5rem auto;";
                if ($textAlign === 'left') $imgStyle .= " margin-left: 0; margin-right: auto;";
                elseif ($textAlign === 'right') $imgStyle .= " margin-left: auto; margin-right: 0;";
                
                if ($src) {
                    return "<figure{$styleAttr}><img src=\"{$src}\" alt=\"{$alt}\" title=\"{$title}\" style=\"{$imgStyle}\"></figure>";
                }
                return '';
            case 'text':
                $text = htmlspecialchars($node['text'] ?? '');
                if (isset($node['marks']) && is_array($node['marks'])) {
                    // Sort marks so that 'link' is always processed last.
                    // This ensures <a> wraps other tags like <span>, allowing inner styles (like color) to override the <a> default color.
                    usort($node['marks'], function($a, $b) {
                        $typeA = $a['type'] ?? '';
                        $typeB = $b['type'] ?? '';
                        if ($typeA === 'link' && $typeB !== 'link') return 1;
                        if ($typeB === 'link' && $typeA !== 'link') return -1;
                        return 0;
                    });

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
            case 'code':
                return "<code>{$text}</code>";
            case 'link':
                $href = !empty($attrs['href']) ? trim($attrs['href']) : '#';
                
                // Fix common user typos like https::/ or https:/
                $href = preg_replace('~^(https?)::?/+~i', '$1://', $href);

                // Ensure absolute URL for external links if missing protocol
                $scheme = parse_url($href, PHP_URL_SCHEME);
                if ($href !== '#' && empty($scheme) && strpos($href, 'mailto:') !== 0 && strpos($href, 'tel:') !== 0 && strpos($href, '/') !== 0) {
                    // Prepend https:// only if it looks like a domain (contains a dot in the first segment)
                    $firstSegment = explode('/', $href)[0];
                    if (strpos($firstSegment, '.') !== false) {
                        $href = 'https://' . ltrim($href, '/');
                    }
                }
                
                // Escape to prevent breaking HTML attributes
                $href = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');

                $target = '_blank';
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
