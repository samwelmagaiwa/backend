<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],

    'allowed_origins' => env('CORS_ALLOWED_ORIGINS') !== null
        ? array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS')))
        : (env('APP_ENV') === 'local'
            ? [
                'http://localhost:3000',     // React/Vue dev server
                'http://localhost:8080',     // Vue CLI dev server
                'http://127.0.0.1:3000',
                'http://127.0.0.1:8080',
            ]
            : [env('APP_URL', 'http://localhost')]),

    'allowed_origins_patterns' => [
        // Allow subdomains in production if needed
        // '/^https:\/\/([a-z0-9\-]+\.)?yourdomain\.com$/',
    ],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', true),
];
