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
            
            if (isset($block['tunes']['alignment']['alignment'])) {
                $localizedBlock['tunes']['alignment']['alignment'] = $ext($block['tunes']['alignment']['alignment']);
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
                    $blockHtml .= "<h{$level} class='{$alignClass}'>" . ($block['data']['text'] ?? '') . "</h{$level}>";
                    break;
                case 'paragraph':
                    $blockHtml .= "<p class='{$alignClass}'>" . ($block['data']['text'] ?? '') . "</p>";
                    break;
                case 'list':
                    $style = $block['data']['style'] ?? 'unordered';
                    
                    if ($style === 'checklist') {
                        $blockHtml .= "<div class='checklist my-8 space-y-3 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/50'>";
                        foreach ($block['data']['items'] ?? [] as $item) {
                            $checked = !empty($item['meta']['checked']);
                            $text = $item['content'] ?? '';
                            
                            $textClass = $checked ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-800 dark:text-slate-200 font-medium';
                            
                            $blockHtml .= "<div class='flex items-start gap-4'>";
                            if ($checked) {
                                $blockHtml .= "<div class='mt-1 flex-shrink-0 w-6 h-6 rounded-full bg-brand-500/10 flex items-center justify-center border border-brand-500/20'>";
                                $blockHtml .= "<svg class='w-4 h-4 text-brand-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M5 13l4 4L19 7'></path></svg>";
                                $blockHtml .= "</div>";
                            } else {
                                $blockHtml .= "<div class='mt-1 flex-shrink-0 w-6 h-6 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'></div>";
                            }
                            $blockHtml .= "<span class='{$textClass} text-lg pt-0.5 leading-relaxed'>{$text}</span>";
                            $blockHtml .= "</div>";
                        }
                        $blockHtml .= "</div>";
                    } else {
                        $tag = ($style === 'ordered') ? 'ol' : 'ul';
                        $blockHtml .= "<{$tag} class='{$alignClass}'>";
                        foreach ($block['data']['items'] ?? [] as $item) {
                            if (is_array($item) && isset($item['content'])) {
                                $blockHtml .= "<li>" . $item['content'] . "</li>";
                            } elseif (is_string($item)) {
                                $blockHtml .= "<li>{$item}</li>";
                            }
                        }
                        $blockHtml .= "</{$tag}>";
                    }
                    break;
                case 'checklist':
                    $blockHtml .= "<div class='checklist my-8 space-y-3 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/50'>";
                    foreach ($block['data']['items'] ?? [] as $item) {
                        $checked = !empty($item['checked']) ? 'checked' : '';
                        $textClass = $checked ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-800 dark:text-slate-200 font-medium';
                        $iconColor = $checked ? 'text-brand-500' : 'text-slate-300 dark:text-slate-600';
                        
                        $blockHtml .= "<div class='flex items-start gap-4'>";
                        
                        if ($checked) {
                            $blockHtml .= "<div class='mt-1 flex-shrink-0 w-6 h-6 rounded-full bg-brand-500/10 flex items-center justify-center border border-brand-500/20'>";
                            $blockHtml .= "<svg class='w-4 h-4 text-brand-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M5 13l4 4L19 7'></path></svg>";
                            $blockHtml .= "</div>";
                        } else {
                            $blockHtml .= "<div class='mt-1 flex-shrink-0 w-6 h-6 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'></div>";
                        }
                        
                        $blockHtml .= "<span class='{$textClass} text-lg pt-0.5 leading-relaxed'>{$item['text']}</span>";
                        $blockHtml .= "</div>";
                    }
                    $blockHtml .= "</div>";
                    break;
                case 'quote':
                    $blockHtml .= "<blockquote class='{$alignClass}'><p>" . ($block['data']['text'] ?? '') . "</p><cite>" . ($block['data']['caption'] ?? '') . "</cite></blockquote>";
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
                    $blockHtml .= "<a href='{$link}' target='_blank' class='btn-primary !text-white !no-underline inline-flex items-center gap-2 text-lg px-8 py-3 rounded-full shadow-glow font-bold'>{$text}</a>";
                    $blockHtml .= "</div>";
                    break;
            }
            
            
            $html .= $blockHtml;
        }
        return $html;
    }
}
