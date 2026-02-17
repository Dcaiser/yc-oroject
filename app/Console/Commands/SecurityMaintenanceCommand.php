<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        if ($this->usesDailyLogging()) {
            $this->line('Skipped manual log cleanup because daily log retention is managed by Laravel logging channels.');
            return;
        }

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

        $storeName = (string) config('cache.default');
        $driver = (string) config("cache.stores.{$storeName}.driver");
        $deleted = 0;

        if ($driver === 'database') {
            $deleted = $this->cleanupDatabaseCache($storeName);
        } elseif ($driver === 'redis') {
            $deleted = $this->cleanupRedisCache($storeName);
        } else {
            $this->line("Skipped cache key scan for unsupported cache driver [{$driver}].");
        }

        $this->line("Security cache cleaned. Removed {$deleted} key(s).");
    }

    private function cleanupDatabaseCache(string $storeName): int
    {
        $table = (string) config("cache.stores.{$storeName}.table", 'cache');
        $prefix = (string) config('cache.prefix', '');

        return DB::table($table)
            ->where(function ($query) use ($prefix) {
                $query->where('key', 'like', "{$prefix}blocked_%")
                    ->orWhere('key', 'like', "{$prefix}login_attempts_%")
                    ->orWhere('key', 'like', '%blocked_%')
                    ->orWhere('key', 'like', '%login_attempts_%');
            })
            ->delete();
    }

    private function cleanupRedisCache(string $storeName): int
    {
        $connectionName = (string) config("cache.stores.{$storeName}.connection", 'cache');
        $redis = app('redis')->connection($connectionName);
        $prefix = (string) config('cache.prefix', '');
        $patterns = [
            "{$prefix}blocked_*",
            "{$prefix}login_attempts_*",
        ];
        $deleted = 0;

        foreach ($patterns as $pattern) {
            $cursor = '0';

            do {
                [$cursor, $keys] = $redis->command('scan', [$cursor, 'MATCH', $pattern, 'COUNT', 100]);

                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $deleted += (int) $redis->command('del', [$key]);
                    }
                }
            } while ((string) $cursor !== '0');
        }

        return $deleted;
    }

    private function usesDailyLogging(): bool
    {
        $defaultChannel = (string) config('logging.default', 'stack');

        if ($defaultChannel === 'daily') {
            return true;
        }

        if ($defaultChannel !== 'stack') {
            return false;
        }

        $stackChannels = config('logging.channels.stack.channels', []);

        return in_array('daily', (array) $stackChannels, true);
    }

    /**
     * Check if any options were provided
     */
    private function hasOptions(): bool
    {
        return $this->option('cleanup-logs') || $this->option('unlock-accounts');
    }
}
