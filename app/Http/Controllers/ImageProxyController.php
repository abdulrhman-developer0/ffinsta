<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $url = $request->get('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(404);
        }

        // Generate a cache key
        $cacheKey = 'img_proxy_' . md5($url);

        // Fetch from cache
        $data = cache()->get($cacheKey);

        if (!$data) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(12)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.9',
                    ])
                    ->get($url);

                if ($response->successful() && !empty($response->body())) {
                    $data = [
                        'body' => base64_encode($response->body()),
                        'content_type' => $response->header('Content-Type') ?? 'image/jpeg',
                    ];
                    // Cache the successful image data for 24 hours
                    cache()->put($cacheKey, $data, 3600 * 24);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Image proxy download failed for URL {$url}: " . $e->getMessage());
            }
        }

        if (!$data) {
            abort(404);
        }

        return response(base64_decode($data['body']))
            ->header('Content-Type', $data['content_type'])
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
