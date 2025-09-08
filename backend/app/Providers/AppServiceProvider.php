<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        // Rate limiters
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return [
                Limit::perMinute(5)->by(strtolower($email) . '|' . $request->ip()),
                Limit::perMinute(50)->by($request->ip()),
            ];
        });

        RateLimiter::for('sensitive', function (Request $request) {
            $key = $request->user()?->id ? 'uid:' . $request->user()->id : 'ip:' . $request->ip();
            return Limit::perMinute(15)->by($key);
        });

        RateLimiter::for('register', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
            ];
        });
    }
}
