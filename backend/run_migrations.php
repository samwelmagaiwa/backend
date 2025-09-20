<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel application
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        api: __DIR__.'/routes/api.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Run migrations
try {
    echo "Running migrations...\n";
    
    // Run the migrate command
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true
    ]);
    
    echo "Migration exit code: " . $exitCode . "\n";
    echo "Migration output:\n";
    echo \Illuminate\Support\Facades\Artisan::output();
    
    // Check if user_access table exists and has the required columns
    echo "\nChecking user_access table structure...\n";
    
    $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM user_access");
    
    $requiredColumns = [
        'hod_signature_path',
        'wellsoft_modules',
        'wellsoft_modules_selected', 
        'jeeva_modules',
        'jeeva_modules_selected',
        'access_type',
        'module_requested_for',
        'temporary_until'
    ];
    
    $existingColumns = array_column($columns, 'Field');
    
    echo "Existing columns in user_access table:\n";
    foreach ($existingColumns as $column) {
        echo "- " . $column . "\n";
    }
    
    echo "\nChecking required columns:\n";
    foreach ($requiredColumns as $column) {
        if (in_array($column, $existingColumns)) {
            echo "✅ " . $column . " - EXISTS\n";
        } else {
            echo "❌ " . $column . " - MISSING\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}