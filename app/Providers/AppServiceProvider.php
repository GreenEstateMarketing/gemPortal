<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Required directly (rather than relying solely on composer's
        // autoload.files cache, which only refreshes on `composer install`/
        // `dump-autoload`) so current_site_direction() is always defined
        // even if a deploy skips that step.
        require_once __DIR__ . '/../helpers.php';
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
