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
        // Force the application to use https for all generated URLs.
        // This is crucial for running behind a reverse proxy that terminates TLS.
        if ($this->app->environment('production') || $this->app->environment('development')) {
            URL::forceScheme('https');
        }
    }
}
