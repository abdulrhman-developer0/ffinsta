<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InstagramScraperService;

class ImageSearchController extends Controller
{
    public function __construct(protected InstagramScraperService $scraperService)
    {
    }

    public function search(Request $request)
    {
        $request->validate(['username' => 'required|string']);
        $result = $this->scraperService->fetchProfilePicture($request->username);
        return response()->json($result);
    }
}
