<?php

use App\Http\Middleware\BrowserBackArrowMiddleware;
use App\Http\Middleware\CheckTokenAbilities;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\BothServiceFormRoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\InputSanitization;
use App\Http\Middleware\XSSProtection;
use App\Http\Middleware\CustomCorsMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        // Security middlewares
        // Include CORS globally to ensure OPTIONS preflight gets headers even if route group middleware is bypassed
        $middleware->use([
            CustomCorsMiddleware::class, // CORS must be first so all responses include CORS headers
            SecurityHeaders::class,      // Apply security headers first
            InputSanitization::class,    // Sanitize inputs early
            XSSProtection::class,        // XSS protection after sanitization
        ]);
        
        // CORS and API-specific middleware - CRITICAL FOR FRONTEND-BACKEND COMMUNICATION
        // Keep CORS in API group as well for explicit ordering within the group
        $middleware->prependToGroup('api', CustomCorsMiddleware::class);
        $middleware->appendToGroup('api', 'throttle:api'); // Rate limiting for API routes
        
        // Removed EnsureFrontendRequestsAreStateful from API group to keep API stateless and avoid CSRF on API routes
        $middleware->alias([
            'browserbackarrow' => BrowserBackArrowMiddleware::class,
            'abilities' => CheckTokenAbilities::class,
            'role' => RoleMiddleware::class,
            'both.service.role' => BothServiceFormRoleMiddleware::class,
            'admin' => App\Http\Middleware\AdminMiddleware::class,
            // Additional security aliases for specific use cases
            'auth.throttle' => 'throttle:auth',
            'sensitive.throttle' => 'throttle:sensitive',
            'upload.throttle' => 'throttle:uploads',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON 401 for API unauthenticated requests instead of redirecting
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });
    })->create();
