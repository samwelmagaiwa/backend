<?php

use App\Http\Middleware\BrowserBackArrowMiddleware;
use App\Http\Middleware\CheckTokenAbilities;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\BothServiceFormRoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->prependToGroup('api', HandleCors::class);
        // Removed EnsureFrontendRequestsAreStateful from API group to keep API stateless and avoid CSRF on API routes
        $middleware->alias([
            'browserbackarrow' => BrowserBackArrowMiddleware::class,
            'abilities' => CheckTokenAbilities::class,
            'role' => RoleMiddleware::class,
            'both.service.role' => BothServiceFormRoleMiddleware::class,
            'admin' => App\Http\Middleware\AdminMiddleware::class,
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
