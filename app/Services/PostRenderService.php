<?php

namespace App\Services;

class PostRenderService
{
    /**
     * Splits unified JSON content back into a standard EditorJS structure for a specific locale.
     * This is used for the Edit UI and internal rendering.
     */
    public function getLocalizedContent($unifiedContent, $locale)
    {
        if (!is_array($unifiedContent) || empty($unifiedContent['blocks'])) {
            // Fallback for old data where content was ['en' => [...], 'ar' => [...]]
            if (isset($unifiedContent['en']) || isset($unifiedContent['ar'])) {
                return $unifiedContent[$locale] ?? null;
            }
            return $unifiedContent;
        }

        $localized = $unifiedContent;
        $localized['blocks'] = [];

        foreach ($unifiedContent['blocks'] as $block) {
            $localizedBlock = $block;
            
            // Helper to extract localized string
            $ext = function($field) use ($locale) {
                if (is_array($field)) {
                    return $field[$locale] ?? ($field['en'] ?? '');
                }
                return $field;
            };

            if (isset($block['data'])) {
                $data = $block['data'];
                switch ($block['type']) {
                    case 'paragraph':
                    case 'header':
                    case 'heading1':
                    case 'heading2':
                    case 'heading3':
                    case 'heading4':
                    case 'heading5':
                        $localizedBlock['data']['text'] = $ext($data['text'] ?? '');
                        break;
                    case 'quote':
                        $localizedBlock['data']['text'] = $ext($data['text'] ?? '');
                        $localizedBlock['data']['caption'] = $ext($data['caption'] ?? '');
                        break;
                    case 'list':
                    case 'checklist':
                        $localizedBlock['data']['items'] = $ext($data['items'] ?? []);
                        break;
                    case 'image':
                    case 'embed':
                        $localizedBlock['data']['caption'] = $ext($data['caption'] ?? '');
                        break;
                    case 'button':
                        $localizedBlock['data']['text'] = $ext($data['text'] ?? '');
                        $localizedBlock['data']['link'] = $ext($data['link'] ?? '');
                        break;
                }
            }
            $localized['blocks'][] = $localizedBlock;
        }
        
        return $localized;
    }

    /**
     * Extracts plain text from the unified EditorJS content for meta description or lists.
     */
    public function getPlainText($unifiedContent, $locale)
    {
        $contentData = $this->getLocalizedContent($unifiedContent, $locale);
        $plainText = '';
        
        if (is_string($contentData)) {
            $plainText = strip_tags($contentData);
        } elseif (is_array($contentData) && isset($contentData['blocks'])) {
            foreach ($contentData['blocks'] as $block) {
                if (in_array($block['type'], ['paragraph', 'header', 'heading1', 'heading2', 'heading3', 'heading4', 'heading5', 'quote'])) {
                    $plainText .= strip_tags($block['data']['text'] ?? '') . ' ';
                }
            }
        }
        return trim($plainText);
    }

    /**
     * Renders EditorJS content into HTML.
     */
    public function renderHtml($unifiedContent, $locale)
    {
        $contentData = $this->getLocalizedContent($unifiedContent, $locale);
        
        if (is_string($contentData)) {
            return $contentData; // Fallback for old HTML
        }
        
        if (!is_array($contentData) || !isset($contentData['blocks'])) {
            return '';
        }
        
        $html = '';
        foreach ($contentData['blocks'] as $block) {
            $alignClass = '';
            if (isset($block['tunes']['alignment']['alignment'])) {
                $align = $block['tunes']['alignment']['alignment'];
                if (in_array($align, ['left', 'center', 'right', 'justify'])) {
                    $alignClass = "text-{$align}";
                }
            }
            
            $blockHtml = '';
            switch ($block['type']) {
                case 'header':
                case 'heading1':
                case 'heading2':
                case 'heading3':
                case 'heading4':
                case 'heading5':
                    $level = $block['data']['level'] ?? null;
                    if (!$level) {
                        $levelNum = (int)str_replace('heading', '', $block['type']);
                        $level = $levelNum > 0 ? $levelNum + 1 : 2;
                    }
                    $blockHtml .= "<h{$level}>" . ($block['data']['text'] ?? '') . "</h{$level}>";
                    break;
                case 'paragraph':
                    $blockHtml .= "<p>" . ($block['data']['text'] ?? '') . "</p>";
                    break;
                case 'list':
                    $style = $block['data']['style'] ?? 'unordered';
                    $tag = ($style === 'ordered') ? 'ol' : 'ul';
                    $blockHtml .= "<{$tag}>";
                    foreach ($block['data']['items'] ?? [] as $item) {
                        if (is_array($item) && isset($item['content'])) {
                            $blockHtml .= "<li>" . $item['content'] . "</li>";
                        } elseif (is_string($item)) {
                            $blockHtml .= "<li>{$item}</li>";
                        }
                    }
                    $blockHtml .= "</{$tag}>";
                    break;
                case 'checklist':
                    $blockHtml .= "<div class='checklist my-6'>";
                    foreach ($block['data']['items'] ?? [] as $item) {
                        $checked = !empty($item['checked']) ? 'checked' : '';
                        $textClass = $checked ? 'line-through text-muted' : 'text-primary';
                        $blockHtml .= "<div class='flex items-start gap-3 mb-2'>";
                        $blockHtml .= "<input type='checkbox' disabled {$checked} class='mt-1.5 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500'>";
                        $blockHtml .= "<span class='{$textClass}'>{$item['text']}</span>";
                        $blockHtml .= "</div>";
                    }
                    $blockHtml .= "</div>";
                    break;
                case 'quote':
                    $blockHtml .= "<blockquote><p>" . ($block['data']['text'] ?? '') . "</p><cite>" . ($block['data']['caption'] ?? '') . "</cite></blockquote>";
                    break;
                case 'image':
                    $url = $block['data']['file']['url'] ?? '';
                    $caption = $block['data']['caption'] ?? '';
                    $withBorder = !empty($block['data']['withBorder']) ? 'border border-slate-200 dark:border-slate-700' : '';
                    $withBackground = !empty($block['data']['withBackground']) ? 'bg-slate-100 dark:bg-slate-800 p-4 rounded-xl' : '';
                    $stretched = !empty($block['data']['stretched']) ? 'w-full' : 'max-w-full rounded-xl shadow-lg mx-auto';
                    
                    if ($url) {
                        $blockHtml .= "<figure class='my-8 {$withBackground}'>";
                        $blockHtml .= "<img src='{$url}' alt='{$caption}' class='{$stretched} {$withBorder}'>";
                        if ($caption) {
                            $blockHtml .= "<figcaption class='text-center text-sm text-muted mt-2'>{$caption}</figcaption>";
                        }
                        $blockHtml .= "</figure>";
                    }
                    break;
                case 'embed':
                    $embedUrl = $block['data']['embed'] ?? '';
                    $caption = $block['data']['caption'] ?? '';
                    if ($embedUrl) {
                        $blockHtml .= "<div class='my-8'>";
                        $blockHtml .= "<div class='relative overflow-hidden rounded-xl shadow-lg' style='padding-top: 56.25%;'>";
                        $blockHtml .= "<iframe src='{$embedUrl}' class='absolute inset-0 w-full h-full' frameborder='0' allowfullscreen></iframe>";
                        $blockHtml .= "</div>";
                        if ($caption) {
                            $blockHtml .= "<p class='text-center text-sm text-muted mt-2'>{$caption}</p>";
                        }
                        $blockHtml .= "</div>";
                    }
                    break;
                case 'delimiter':
                    $blockHtml .= "<hr class='my-12 border-t-2 border-slate-200 dark:border-slate-700 w-24 mx-auto rounded-full'>";
                    break;
                case 'button':
                    $link = $block['data']['link'] ?? '#';
                    $text = $block['data']['text'] ?? 'Click Here';
                    $blockHtml .= "<div class='my-8 " . ($alignClass ?: 'text-center') . "'>";
                    $blockHtml .= "<a href='{$link}' target='_blank' class='btn-primary inline-flex items-center gap-2 text-lg px-8 py-3 rounded-full shadow-glow font-bold'>{$text}</a>";
                    $blockHtml .= "</div>";
                    break;
            }
            
            if ($alignClass && $block['type'] !== 'button') {
                $html .= "<div class='{$alignClass} w-full'>{$blockHtml}</div>";
            } else {
                $html .= $blockHtml;
            }
        }
        return $html;
    }
}
