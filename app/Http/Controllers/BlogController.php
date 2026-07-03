<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Hashtag;
use App\Services\PostRenderService;

class BlogController extends Controller
{
    public function index(Request $request, PostRenderService $renderService, $slug = null)
    {
        $query = Post::where('is_active', true)->orderBy('id', 'desc');
        $selectedHashtag = null;
        
        if ($slug) {
            $selectedHashtag = Hashtag::where('slug', $slug)->firstOrFail();
            $query->whereHas('hashtags', function($q) use ($selectedHashtag) {
                $q->where('hashtags.id', $selectedHashtag->id);
            });
        } elseif ($request->has('hashtag')) {
            // Fallback for old query param
            $selectedHashtag = Hashtag::where('slug', $request->hashtag)->firstOrFail();
            $query->whereHas('hashtags', function($q) use ($selectedHashtag) {
                $q->where('hashtags.id', $selectedHashtag->id);
            });
        }
        
        $posts = $query->paginate(6);
        
        // Add plain text directly to the paginator items to keep views clean
        $locale = app()->getLocale();
        $posts->getCollection()->transform(function ($post) use ($renderService, $locale) {
            $post->rendered_plain_text = $renderService->getPlainText($post->content, $locale);
            return $post;
        });

        $hashtags = Hashtag::whereHas('posts', function($q) {
            $q->where('is_active', true);
        })->get();
        
        return view('blog.index', compact('posts', 'hashtags', 'selectedHashtag'));
    }

    public function show(PostRenderService $renderService, $slug)
    {
        $post = Post::where('is_active', true)
            ->where(function($q) use ($slug) {
                $q->where('slug->en', $slug)->orWhere('slug->ar', $slug);
            })->firstOrFail();
            
        // Increment views
        $post->increment('views');
        
        $locale = app()->getLocale();
        $renderedContent = $renderService->renderHtml($post->content, $locale);
        $plainText = $renderService->getPlainText($post->content, $locale);
            
        return view('blog.show', compact('post', 'renderedContent', 'plainText'));
    }
}

