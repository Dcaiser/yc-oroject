<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            $request = request();

            if ($request->getHost()) {
                $rootUrl = $request->getScheme().'://'.$request->getHost();

                $port = $request->getPort();
                if ($port && ! in_array($port, [80, 443], true)) {
                    $rootUrl .= ':'.$port;
                }

                URL::forceRootUrl($rootUrl);
                config(['app.url' => $rootUrl]);

                if ($request->isSecure()) {
                    URL::forceScheme('https');
                }
            }
        }
    }
}