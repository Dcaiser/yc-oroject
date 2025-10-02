<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        
        // Log important changes
        if (array_key_exists('email', $changes) || 
            array_key_exists('role', $changes) || 
            array_key_exists('password', $changes)) {
            
            Log::info('User profile updated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'changes' => array_keys($changes),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
        
        // Log last login update separately
        if (array_key_exists('last_login_at', $changes)) {
            Log::info('User login tracked', [
                'user_id' => $user->id,
                'email' => $user->email,
                'last_login' => $changes['last_login_at'],
                'ip' => request()->ip()
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Log::warning('User account deleted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'deleted_by_ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}