<?php

use App\Http\Middleware\BrowserBackArrowMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        // $middleware->prependToGroup('api', HandleCors::class);
        // $middleware->prependToGroup('api', EnsureFrontendRequestsAreStateful::class);
        // $middleware->alias([
        //     'browserbackarrow' => BrowserBackArrowMiddleware::class,
        // ]);
    })
    ->withExceptions(function ($exceptions) {
        // Configure exception handling here if needed
    })->create();