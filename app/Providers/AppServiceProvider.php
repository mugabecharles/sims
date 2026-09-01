<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix for MariaDB/MySQL key length limit with utf8mb4
        Schema::defaultStringLength(191);

        // Force the application root URL so route() and redirect() generate
        // correct full URLs when running in a subdirectory (e.g. WAMP /sims/public).
        // We read directly from $_ENV (populated by phpdotenv) since getenv()
        // may be unavailable depending on PHP variables_order setting.
        $appUrl = $_ENV['APP_URL'] ?? env('APP_URL') ?? config('app.url');

        if ($appUrl && $appUrl !== 'http://localhost') {
            URL::forceRootUrl($appUrl);
        }

        if (str_starts_with((string) $appUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }
}
