<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginAttemptMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'login_attempts_' . $ip;
        
        // Check if IP is temporarily blocked
        if (Cache::has('blocked_' . $ip)) {
            $minutes = Cache::get('blocked_' . $ip);
            return response()->json([
                'message' => "IP Anda diblokir sementara. Silakan coba lagi setelah {$minutes} menit.",
                'error' => 'too_many_attempts'
            ], 429);
        }

        // Log suspicious activity
        $attempts = Cache::get($key, 0);
        if ($attempts >= 5) {
            \Log::warning('Suspicious login activity detected', [
                'ip' => $ip,
                'attempts' => $attempts,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            
            // Block IP for 30 minutes after 10 failed attempts
            if ($attempts >= 10) {
                Cache::put('blocked_' . $ip, 30, now()->addMinutes(30));
                \Log::alert('IP blocked due to excessive login attempts', [
                    'ip' => $ip,
                    'attempts' => $attempts
                ]);
            }
        }

        return $next($request);
    }
}