<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $expiresAt = now()->addMinutes(5); // User dianggap offline jika tidak ada aktivitas selama 5 menit
            Cache::put('user-is-online-' . Auth::id(), true, $expiresAt);
            
            // Opsional: Update last_seen di database jika kolom ada (untuk persistensi jangka panjang)
            // User::where('id', Auth::id())->update(['last_seen' => now()]);
        }

        return $next($request);
    }
}
