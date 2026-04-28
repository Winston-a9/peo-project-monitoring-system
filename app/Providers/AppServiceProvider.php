<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        
        // Configure rate limiters
        $this->configureRateLimiters();
    }
    
    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiters(): void
    {
        RateLimiter::for('export', function (Request $request) {
            // Allow 10 export requests per minute per user
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
