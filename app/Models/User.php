<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    // Helper methods untuk role checking
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    // Method untuk checking apakah user bisa akses level tertentu
    public function canAccessManager()
    {
        return in_array($this->role, ['manager', 'admin']);
    }

    public function canAccessStaff()
    {
        return in_array($this->role, ['staff', 'manager', 'admin']);
    }

    // Security helper methods
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function lockAccount($minutes = 30)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes)
        ]);
    }

    public function unlockAccount()
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0
        ]);
    }

    public function incrementFailedAttempts()
    {
        $this->increment('failed_login_attempts');
        
        // Auto lock after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount(30); // Lock for 30 minutes
        }
    }

    public function resetFailedAttempts()
    {
        $this->update(['failed_login_attempts' => 0]);
    }
}