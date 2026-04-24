<?php

namespace App\Providers;

use Carbon\Carbon;
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
        // Explicit timezone handling for all date/time operations
        $timezone = config('app.timezone');
        
        // Set PHP's default timezone
        date_default_timezone_set($timezone);
        
        // Set Carbon's timezone and locale
        Carbon::setLocale(config('app.locale'));
        // Note: Carbon respects PHP's date_default_timezone_set, but being explicit here
    }
}
