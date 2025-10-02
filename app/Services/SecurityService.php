<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SecurityService
{
    /**
     * Check if this is a login from a new device
     */
    public function isNewDevice(User $user, Request $request): bool
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();
        
        // Create a device fingerprint
        $deviceFingerprint = md5($userAgent . $ip);
        
        // Check if this device has been used before
        $cacheKey = "user_devices_{$user->id}";
        $knownDevices = Cache::get($cacheKey, []);
        
        return !in_array($deviceFingerprint, $knownDevices);
    }

    /**
     * Register a new device for the user
     */
    public function registerDevice(User $user, Request $request): void
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();
        $deviceFingerprint = md5($userAgent . $ip);
        
        $cacheKey = "user_devices_{$user->id}";
        $knownDevices = Cache::get($cacheKey, []);
        
        if (!in_array($deviceFingerprint, $knownDevices)) {
            $knownDevices[] = $deviceFingerprint;
            
            // Keep only the last 10 devices
            if (count($knownDevices) > 10) {
                $knownDevices = array_slice($knownDevices, -10);
            }
            
            Cache::put($cacheKey, $knownDevices, now()->addDays(90));
            
            // Log new device registration
            Log::info('New device registered for user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'device_fingerprint' => $deviceFingerprint
            ]);
        }
    }

    /**
     * Get device info from user agent
     */
    public function getDeviceInfo(string $userAgent): array
    {
        $device = 'Unknown';
        $browser = 'Unknown';
        $os = 'Unknown';
        
        // Simple device detection
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            $device = 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            $device = 'Tablet';
        } else {
            $device = 'Desktop';
        }
        
        // Browser detection
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }
        
        // OS detection
        if (strpos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $os = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            $os = 'iOS';
        }
        
        return compact('device', 'browser', 'os');
    }

    /**
     * Check for suspicious login patterns
     */
    public function checkSuspiciousActivity(User $user, Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Check for rapid location changes (different IP ranges)
        $lastLoginKey = "last_login_ip_{$user->id}";
        $lastIp = Cache::get($lastLoginKey);
        
        if ($lastIp && $this->isDifferentLocation($lastIp, $ip)) {
            $timeDiff = now()->diffInMinutes($user->last_login_at ?? now()->subHour());
            
            // Suspicious if location changed within 10 minutes
            if ($timeDiff < 10) {
                Log::warning('Suspicious login: Rapid location change detected', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'last_ip' => $lastIp,
                    'current_ip' => $ip,
                    'time_diff_minutes' => $timeDiff
                ]);
                return true;
            }
        }
        
        Cache::put($lastLoginKey, $ip, now()->addHour());
        return false;
    }

    /**
     * Simple check for different geographical locations based on IP
     */
    private function isDifferentLocation(string $ip1, string $ip2): bool
    {
        // Simple check - if first two octets are different, consider as different location
        $parts1 = explode('.', $ip1);
        $parts2 = explode('.', $ip2);
        
        return ($parts1[0] !== $parts2[0] || $parts1[1] !== $parts2[1]);
    }
}