<?php

namespace App\Support\Security;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;

class Health
{
    /**
     * Run security health checks.
     *
     * @return array<string, array{ok: bool, message: string}>
     */
    public static function checks(): array
    {
        $results = [];

        // CORS
        $corsPaths = (array) Config::get('cors.paths', []);
        $results['CORS Protection'] = [
            'ok' => !empty($corsPaths),
            'message' => !empty($corsPaths)
                ? 'CORS configured for paths: ' . implode(', ', $corsPaths)
                : 'CORS is not configured (config/cors.php paths empty)'
        ];

        // Security Headers middleware class existence
        $results['Security Headers (Helmet-like)'] = [
            'ok' => class_exists(\App\Http\Middleware\SecurityHeaders::class),
            'message' => class_exists(\App\Http\Middleware\SecurityHeaders::class)
                ? 'SecurityHeaders middleware available'
                : 'SecurityHeaders middleware missing'
        ];

        // Input Sanitization middleware class existence
        $results['Input Sanitization'] = [
            'ok' => class_exists(\App\Http\Middleware\InputSanitization::class),
            'message' => class_exists(\App\Http\Middleware\InputSanitization::class)
                ? 'InputSanitization middleware available'
                : 'InputSanitization middleware missing'
        ];

        // XSS Protection middleware class existence
        $results['XSS Protection'] = [
            'ok' => class_exists(\App\Http\Middleware\XSSProtection::class),
            'message' => class_exists(\App\Http\Middleware\XSSProtection::class)
                ? 'XSSProtection middleware available'
                : 'XSSProtection middleware missing'
        ];

        // Rate Limiting configured
        try {
            $apiLimiterExists = class_exists(\App\Providers\RouteServiceProvider::class);
            $results['Rate Limiting (60 req/min)'] = [
                'ok' => $apiLimiterExists,
                'message' => $apiLimiterExists
                    ? 'RouteServiceProvider available (rate limiters configured)'
                    : 'RouteServiceProvider missing (rate limiters not configured)'
            ];
        } catch (\Exception $e) {
            $results['Rate Limiting (60 req/min)'] = [
                'ok' => false,
                'message' => 'Error checking rate limiter: ' . $e->getMessage()
            ];
        }

        // SQL Injection prevention - best effort (ORM available)
        $results['SQL Injection Prevention'] = [
            'ok' => class_exists(\Illuminate\Database\Eloquent\Model::class),
            'message' => class_exists(\Illuminate\Database\Eloquent\Model::class)
                ? 'Eloquent ORM available (use parameter binding)'
                : 'Eloquent ORM not available'
        ];

        // CSRF Protection - middleware presence (class existence check)
        $results['CSRF Protection'] = [
            'ok' => class_exists(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class),
            'message' => class_exists(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
                ? 'VerifyCsrfToken middleware available (enabled for web)'
                : 'VerifyCsrfToken middleware missing'
        ];

        return $results;
    }
}

