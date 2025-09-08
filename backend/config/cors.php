<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => env('CORS_ALLOWED_ORIGINS') !== null
        ? array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS')))
        : (env('APP_ENV') === 'local'
            ? ['http://localhost:8080', 'http://127.0.0.1:8080']
            : [env('APP_URL', 'http://localhost')]),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', true),
];
