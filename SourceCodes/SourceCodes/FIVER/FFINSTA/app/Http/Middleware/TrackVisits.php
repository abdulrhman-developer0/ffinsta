<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $date = \Carbon\Carbon::today()->toDateString();
        $userAgent = $request->userAgent();
        
        // Track unique visit per IP per day
        \App\Models\Visit::firstOrCreate(
            ['ip_address' => $ip, 'visited_date' => $date],
            ['user_agent' => $userAgent, 'user_id' => auth()->id()]
        );

        return $next($request);
    }
}
