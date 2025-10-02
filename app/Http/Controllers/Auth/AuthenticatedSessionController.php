<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\SecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        $user = Auth::user();
        
        // Check for suspicious activity
        $isSuspicious = $this->securityService->checkSuspiciousActivity($user, $request);
        
        // Check if this is a new device
        $isNewDevice = $this->securityService->isNewDevice($user, $request);
        
        // Register the device if new
        if ($isNewDevice) {
            $this->securityService->registerDevice($user, $request);
        }
        
        // Enhanced login logging
        $deviceInfo = $this->securityService->getDeviceInfo($request->userAgent());
        
        \Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_info' => $deviceInfo,
            'is_new_device' => $isNewDevice,
            'is_suspicious' => $isSuspicious,
            'login_time' => now()
        ]);

        // Update last login timestamp
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        // Add warning message for new device
        if ($isNewDevice) {
            session()->flash('warning', 'Login dari perangkat baru terdeteksi. Jika ini bukan Anda, segera ubah password.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Log logout activity
        if ($user) {
            \Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah berhasil logout.');
    }
}
