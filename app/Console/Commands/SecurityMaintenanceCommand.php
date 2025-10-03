<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SecurityMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:maintenance {--cleanup-logs} {--unlock-accounts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform security maintenance tasks like cleaning old logs and unlocking expired accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting security maintenance...');
        
        if ($this->option('cleanup-logs') || !$this->hasOptions()) {
            $this->cleanupLogs();
        }
        
        if ($this->option('unlock-accounts') || !$this->hasOptions()) {
            $this->unlockExpiredAccounts();
        }
        
        $this->cleanupCache();
        
        $this->info('Security maintenance completed successfully!');
    }

    /**
     * Clean up old log files
     */
    private function cleanupLogs(): void
    {
        $this->info('Cleaning up old log files...');
        
        $logPath = storage_path('logs');
        $files = glob($logPath . '/laravel-*.log');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-30 days')) {
                unlink($file);
                $cleaned++;
            }
        }
        
        $this->line("Cleaned {$cleaned} old log files.");
    }

    /**
     * Unlock accounts that have expired lock periods
     */
    private function unlockExpiredAccounts(): void
    {
        $this->info('Unlocking expired locked accounts...');
        
        $unlockedCount = \App\Models\User::where('locked_until', '<', now())
            ->whereNotNull('locked_until')
            ->update([
                'locked_until' => null,
                'failed_login_attempts' => 0
            ]);
            
        $this->line("Unlocked {$unlockedCount} expired accounts.");
    }

    /**
     * Clean up security-related cache entries
     */
    private function cleanupCache(): void
    {
        $this->info('Cleaning up security cache...');
        
        // Clean up blocked IPs cache
        $cache = app('cache');
        $keys = $cache->getRedis()->keys('*blocked_*');
        
        foreach ($keys as $key) {
            $cache->forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
        
        // Clean up login attempts cache
        $attemptKeys = $cache->getRedis()->keys('*login_attempts_*');
        
        foreach ($attemptKeys as $key) {
            $cache->forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
        
        $this->line('Security cache cleaned.');
    }

    /**
     * Check if any options were provided
     */
    private function hasOptions(): bool
    {
        return $this->option('cleanup-logs') || $this->option('unlock-accounts');
    }
}
