<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $existingHashtags = Hashtag::pluck('name');
        return view('admin.posts.create', compact('existingHashtags'));
    }

    public function store(Request $request)
    {
        $editorJsRule = function ($attribute, $value, $fail) {
            if (!$value) return;
            $data = is_string($value) ? json_decode($value, true) : $value;
            if (!$data || !isset($data['blocks']) || count($data['blocks']) === 0) {
                $lang = str_replace('content.', '', $attribute);
                $fail("The content ($lang) must have at least one block.");
            }
        };

        $validated = $request->validate([
            'title.en' => 'required_without:title.ar|nullable|string|max:255',
            'title.ar' => 'required_without:title.en|nullable|string|max:255',
            'content.en' => ['required_without:content.ar', 'nullable', 'string', $editorJsRule],
            'content.ar' => ['required_without:content.en', 'nullable', 'string', $editorJsRule],
            'image' => 'required|image|max:2048',
            'is_active' => 'boolean',
            'hashtags' => 'nullable|string', // Comma separated tags
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        
        $slug = $this->generateUniqueSlug($validated['title']['en'] ?? '', $validated['title']['ar'] ?? '');
        
        $contentEn = json_decode($validated['content']['en'] ?? '{}', true) ?: $validated['content']['en'];
        $contentAr = json_decode($validated['content']['ar'] ?? '{}', true) ?: $validated['content']['ar'];
        
        $unifiedContent = $this->mergeEditorJsContent($contentEn, $contentAr);

        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $unifiedContent,
            'is_active' => $validated['is_active'],
        ]);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('cover');
        }

        $this->syncHashtags($post, $request->input('hashtags'));

        return redirect()->route('admin.posts.index')->with('success', __('Post created successfully.'));
    }

    public function edit(Post $post, \App\Services\PostRenderService $renderService)
    {
        $hashtags = $post->hashtags->pluck('name')->implode(',');
        $existingHashtags = Hashtag::pluck('name');
        
        $editorContentEn = $renderService->getLocalizedContent($post->content, 'en');
        $editorContentAr = $renderService->getLocalizedContent($post->content, 'ar');
        
        return view('admin.posts.edit', compact('post', 'hashtags', 'existingHashtags', 'editorContentEn', 'editorContentAr'));
    }

    public function update(Request $request, Post $post)
    {
        $editorJsRule = function ($attribute, $value, $fail) {
            if (!$value) return;
            $data = is_string($value) ? json_decode($value, true) : $value;
            if (!$data || !isset($data['blocks']) || count($data['blocks']) === 0) {
                $lang = str_replace('content.', '', $attribute);
                $fail("The content ($lang) must have at least one block.");
            }
        };

        $validated = $request->validate([
            'title.en' => 'required_without:title.ar|nullable|string|max:255',
            'title.ar' => 'required_without:title.en|nullable|string|max:255',
            'content.en' => ['nullable', 'string', $editorJsRule],
            'content.ar' => ['nullable', 'string', $editorJsRule],
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'hashtags' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        
        $slug = $this->generateUniqueSlug($validated['title']['en'] ?? '', $validated['title']['ar'] ?? '', $post->id);
        
        $updateData = [
            'title' => $validated['title'],
            'slug' => $slug,
            'is_active' => $validated['is_active'],
        ];

        // Only update content if there was a recent activity (tracked via hidden input)
        if ($request->filled('content_last_activity')) {
            $contentEn = json_decode($validated['content']['en'] ?? '{}', true) ?: $validated['content']['en'];
            $contentAr = json_decode($validated['content']['ar'] ?? '{}', true) ?: $validated['content']['ar'];
            
            $unifiedContent = $this->mergeEditorJsContent($contentEn, $contentAr);
            $updateData['content'] = $unifiedContent;
        }

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('cover');
        }

        $post->update($updateData);

        $this->syncHashtags($post, $request->input('hashtags'));

        return redirect()->route('admin.posts.index')->with('success', __('Post updated successfully.'));
    }

    private function generateUniqueSlug($titleEn, $titleAr, $postId = null)
    {
        $baseEn = $titleEn ? Str::slug($titleEn) : '';
        $baseAr = $titleAr ? (Str::slug($titleAr) ?: str_replace(' ', '-', $titleAr)) : '';
        
        $slugEn = $baseEn;
        $slugAr = $baseAr;
        
        $counter = 1;
        while (true) {
            $query = Post::query();
            if ($postId) {
                $query->where('id', '!=', $postId);
            }
            
            $exists = $query->where(function($q) use ($slugEn, $slugAr) {
                if ($slugEn) $q->orWhere('slug->en', $slugEn);
                if ($slugAr) $q->orWhere('slug->ar', $slugAr);
            })->exists();
            
            if (!$exists) {
                break;
            }
            
            if ($baseEn) $slugEn = $baseEn . '-' . $counter;
            if ($baseAr) $slugAr = $baseAr . '-' . $counter;
            $counter++;
        }
        
        return [
            'en' => $slugEn,
            'ar' => $slugAr,
        ];
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', __('Post deleted successfully.'));
    }
    
    private function syncHashtags(Post $post, ?string $hashtagsString)
    {
        if (empty($hashtagsString)) {
            $post->hashtags()->detach();
            return;
        }
        
        $tags = array_filter(array_map('trim', explode(',', $hashtagsString)));
        $hashtagIds = [];
        
        foreach ($tags as $tagName) {
            $hashtag = Hashtag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName) ?: str_replace(' ', '-', $tagName)]
            );
            $hashtagIds[] = $hashtag->id;
        }
        
        $post->hashtags()->sync($hashtagIds);
    }
    
    public function uploadEditorMedia(Request $request)
    {
        $request->validate([
            'image' => 'required|file|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $media = auth()->user()->addMediaFromRequest('image')->toMediaCollection('editor_media');

            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $media->getUrl(),
                ]
            ]);
        }

        return response()->json(['success' => 0]);
    }

    private function mergeEditorJsContent($en, $ar)
    {
        if (!$en || empty($en['blocks'])) {
            return $en ?: $ar;
        }

        $unified = $en;
        $unified['blocks'] = [];

        $arBlocks = $ar['blocks'] ?? [];

        foreach ($en['blocks'] as $i => $enBlock) {
            $arBlock = $arBlocks[$i] ?? $enBlock; // Fallback to EN if AR is missing at this index
            
            $unifiedBlock = $enBlock;
            
            $mergeField = function($enField, $arField) {
                return ['en' => $enField, 'ar' => $arField];
            };

            if (isset($enBlock['data'])) {
                $enData = $enBlock['data'];
                $arData = $arBlock['data'] ?? [];
                
                switch ($enBlock['type']) {
                    case 'paragraph':
                    case 'header':
                    case 'heading1':
                    case 'heading2':
                    case 'heading3':
                    case 'heading4':
                    case 'heading5':
                        $unifiedBlock['data']['text'] = $mergeField($enData['text'] ?? '', $arData['text'] ?? '');
                        break;
                    case 'quote':
                        $unifiedBlock['data']['text'] = $mergeField($enData['text'] ?? '', $arData['text'] ?? '');
                        $unifiedBlock['data']['caption'] = $mergeField($enData['caption'] ?? '', $arData['caption'] ?? '');
                        break;
                    case 'list':
                    case 'checklist':
                        $unifiedBlock['data']['items'] = $mergeField($enData['items'] ?? [], $arData['items'] ?? []);
                        break;
                    case 'image':
                    case 'embed':
                        $unifiedBlock['data']['caption'] = $mergeField($enData['caption'] ?? '', $arData['caption'] ?? '');
                        break;
                    case 'button':
                        $unifiedBlock['data']['text'] = $mergeField($enData['text'] ?? '', $arData['text'] ?? '');
                        $unifiedBlock['data']['link'] = $mergeField($enData['link'] ?? '', $arData['link'] ?? '');
                        break;
                }
            }
            
            // Merge block tunes (alignment)
            if (isset($enBlock['tunes']['alignment']['alignment']) || isset($arBlock['tunes']['alignment']['alignment'])) {
                $enAlign = $enBlock['tunes']['alignment']['alignment'] ?? null;
                $arAlign = $arBlock['tunes']['alignment']['alignment'] ?? null;
                
                if (!isset($unifiedBlock['tunes'])) $unifiedBlock['tunes'] = [];
                if (!isset($unifiedBlock['tunes']['alignment'])) $unifiedBlock['tunes']['alignment'] = [];
                
                $unifiedBlock['tunes']['alignment']['alignment'] = [
                    'en' => $enAlign,
                    'ar' => $arAlign
                ];
            }

            $unified['blocks'][] = $unifiedBlock;
        }

        return $unified;
    }
}
