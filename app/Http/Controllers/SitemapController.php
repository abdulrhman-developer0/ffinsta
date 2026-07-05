<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // Add static pages
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/blog')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/privacy-policy')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        $sitemap->add(Url::create('/terms-of-service')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        $sitemap->add(Url::create('/refund-policy')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        // Add dynamic blog posts
        Post::where('is_active', true)->get()->each(function (Post $post) use ($sitemap) {
            $slugs = [];
            if (!empty($post->slug['en'])) $slugs[] = $post->slug['en'];
            if (!empty($post->slug['ar'])) $slugs[] = $post->slug['ar'];
            
            foreach (array_unique($slugs) as $slug) {
                $sitemap->add(
                    Url::create("/blog/{$slug}")
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            }
        });

        return $sitemap->toResponse(request());
    }
}
