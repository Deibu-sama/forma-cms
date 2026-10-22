<?php

namespace App\\Providers;

use Illuminate\\Support\\Facades\\URL;
use Illuminate\\Support\\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Render terminates HTTPS at its proxy, so Laravel may see the
        // internal request as HTTP unless proxy headers are trusted.
        // Force secure URLs in production to prevent browser mixed-content
        // blocking for Vite assets, favicons, and generated links.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
