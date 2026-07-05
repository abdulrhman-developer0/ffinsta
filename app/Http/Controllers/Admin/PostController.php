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
        $tipTapRule = function ($attribute, $value, $fail) {
            if (!$value) return;
            $data = is_string($value) ? json_decode($value, true) : $value;
            if (!$data || !isset($data['type']) || $data['type'] !== 'doc') {
                $lang = str_replace('content.', '', $attribute);
                $fail("The content ($lang) is invalid.");
            }
        };

        $validated = $request->validate([
            'title.en' => 'required_without:title.ar|nullable|string|max:255',
            'title.ar' => 'required_without:title.en|nullable|string|max:255',
            'content.en' => ['required_without:content.ar', 'nullable', 'string', $tipTapRule],
            'content.ar' => ['required_without:content.en', 'nullable', 'string', $tipTapRule],
            'image' => 'required|image|max:2048',
            'is_active' => 'boolean',
            'hashtags' => 'nullable|string', // Comma separated tags
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        
        $slug = $this->generateUniqueSlug($validated['title']['en'] ?? '', $validated['title']['ar'] ?? '');
        
        $contentEn = json_decode($validated['content']['en'] ?? '{}', true) ?: $validated['content']['en'];
        $contentAr = json_decode($validated['content']['ar'] ?? '{}', true) ?: $validated['content']['ar'];
        
        $unifiedContent = [
            'en' => $contentEn,
            'ar' => $contentAr,
        ];

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
        $tipTapRule = function ($attribute, $value, $fail) {
            if (!$value) return;
            $data = is_string($value) ? json_decode($value, true) : $value;
            if (!$data || !isset($data['type']) || $data['type'] !== 'doc') {
                $lang = str_replace('content.', '', $attribute);
                $fail("The content ($lang) is invalid.");
            }
        };

        $validated = $request->validate([
            'title.en' => 'required_without:title.ar|nullable|string|max:255',
            'title.ar' => 'required_without:title.en|nullable|string|max:255',
            'content.en' => ['nullable', 'string', $tipTapRule],
            'content.ar' => ['nullable', 'string', $tipTapRule],
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

        $contentEn = json_decode($validated['content']['en'] ?? '{}', true) ?: $validated['content']['en'];
        $contentAr = json_decode($validated['content']['ar'] ?? '{}', true) ?: $validated['content']['ar'];
        
        $unifiedContent = [
            'en' => $contentEn,
            'ar' => $contentAr,
        ];
        $updateData['content'] = $unifiedContent;

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('cover');
        }

        $post->update($updateData);

        $this->syncHashtags($post, $request->input('hashtags'));

        return redirect()->back()->with('success', __('Post updated successfully.'));
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

}
