<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('deploy:hostinger-ready', function () {
    $this->info('Checking Hostinger production readiness...');

    $errors = 0;
    $warnings = 0;

    $appEnv = (string) config('app.env');
    $appDebug = (bool) config('app.debug');

    if ($appEnv !== 'production') {
        $warnings++;
        $this->warn("APP_ENV is '{$appEnv}'. Recommended: production");
    } else {
        $this->line('✓ APP_ENV is production');
    }

    if ($appDebug) {
        $errors++;
        $this->error('APP_DEBUG is true. Set APP_DEBUG=false for production.');
    } else {
        $this->line('✓ APP_DEBUG is false');
    }

    $appUrl = (string) config('app.url');
    if ($appUrl === '' || !str_starts_with($appUrl, 'https://')) {
        $warnings++;
        $this->warn("APP_URL is '{$appUrl}'. Recommended HTTPS URL.");
    } else {
        $this->line("✓ APP_URL uses HTTPS ({$appUrl})");
    }

    $hotFile = public_path('hot');
    if (File::exists($hotFile)) {
        $errors++;
        $this->error('Found public/hot. Remove it for production (Vite dev-server artifact).');
    } else {
        $this->line('✓ public/hot not found');
    }

    $routeCacheFile = base_path('bootstrap/cache/routes-v7.php');
    if (File::exists($routeCacheFile)) {
        $warnings++;
        $this->warn('bootstrap/cache/routes-v7.php exists (normal after route:cache). Ensure it is generated on server, not copied from local build.');
    } else {
        $this->line('✓ bootstrap/cache/routes-v7.php not found');
    }

    $buildManifest = public_path('build/manifest.json');
    if (!File::exists($buildManifest)) {
        $errors++;
        $this->error('Missing public/build/manifest.json. Run npm run build before deploy.');
    } else {
        $this->line('✓ Vite build manifest exists');
    }

    $storageWritable = is_writable(storage_path());
    $cacheWritable = is_writable(base_path('bootstrap/cache'));

    if (!$storageWritable) {
        $errors++;
        $this->error('storage/ is not writable.');
    } else {
        $this->line('✓ storage/ writable');
    }

    if (!$cacheWritable) {
        $errors++;
        $this->error('bootstrap/cache is not writable.');
    } else {
        $this->line('✓ bootstrap/cache writable');
    }

    $controllerFile = app_path('Http/Controllers/PosController.php');
    if (!File::exists($controllerFile)) {
        $errors++;
        $this->error('Missing app/Http/Controllers/PosController.php (case-sensitive issue risk on Linux).');
    } else {
        $this->line('✓ PosController file casing is Linux-safe');
    }

    $this->newLine();
    $this->line("Summary: {$errors} error(s), {$warnings} warning(s)");

    if ($errors > 0) {
        $this->error('Not ready for production yet. Fix errors above.');
        return self::FAILURE;
    }

    if ($warnings > 0) {
        $this->warn('Ready with warnings. Review warnings before go-live.');
        return self::SUCCESS;
    }

    $this->info('Production-ready for Hostinger.');
    return self::SUCCESS;
})->purpose('Check Laravel app readiness for Hostinger production deployment');
