<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request as HttpRequest;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        // Global API rate limiting: 60 requests per minute per IP
        RateLimiter::for('api', function (HttpRequest $request) {
            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'Too Many Requests',
                        'message' => 'You have exceeded the rate limit of 60 requests per minute.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Stricter rate limiting for authentication endpoints
        RateLimiter::for('auth', function (HttpRequest $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'Too Many Authentication Attempts',
                        'message' => 'Too many login attempts. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Very strict rate limiting for sensitive operations
        RateLimiter::for('sensitive', function (HttpRequest $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'Rate Limit Exceeded',
                        'message' => 'Too many requests for sensitive operations.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Per-user rate limiting (for authenticated users)
        RateLimiter::for('user', function (HttpRequest $request) {
            $userId = $request->user()?->id ?: $request->ip();
            
            return Limit::perMinute(100)
                ->by($userId)
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'User Rate Limit Exceeded',
                        'message' => 'You have exceeded your personal rate limit.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // File upload rate limiting
        RateLimiter::for('uploads', function (HttpRequest $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'Upload Rate Limit Exceeded',
                        'message' => 'Too many file upload attempts.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Login rate limiting (stricter than general auth)
        RateLimiter::for('login', function (HttpRequest $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'Too Many Login Attempts',
                        'message' => 'Too many login attempts. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Registration rate limiting
        RateLimiter::for('register', function (HttpRequest $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (HttpRequest $request, array $headers) {
                    return response()->json([
                        'error' => 'Too Many Registration Attempts',
                        'message' => 'Too many registration attempts. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });
    }
}
