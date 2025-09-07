<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
// Passport routes are auto-registered in current Passport versions

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
        // OTP limiter already defined in routes boot earlier
        RateLimiter::for('otp', function (Request $request) {
            $phone = (string) ($request->input('phone') ?? 'unknown');
            return [
                Limit::perMinute(5)->by($phone.'|'.$request->ip()),
                Limit::perMinute(10)->by($request->ip()),
            ];
        });

        // Anti-fraud limiter for critical endpoints
        RateLimiter::for('critical', function (Request $request) {
            $userId = optional($request->user('api') ?? $request->user())->id ?? $request->ip();
            return [
                Limit::perMinute(20)->by('u:'.$userId),
                Limit::perHour(200)->by('u:'.$userId),
            ];
        });
    }
}
