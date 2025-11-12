<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwaggerController;

/*
|--------------------------------------------------------------------------
| Swagger Routes
|--------------------------------------------------------------------------
|
| Routes for modern Swagger API documentation
|
*/

Route::group(['middleware' => ['web']], function () {
// Modern API Documentation Route (redirect legacy UI route to the unified docs)
    Route::get('api/documentation', function () {
        return redirect('/api-docs-modern');
    })->name('swagger.modern.api');
    
    // Custom assets route
    Route::get('swagger-assets/{asset}', [SwaggerController::class, 'asset'])
        ->where('asset', '.*')
        ->name('swagger.modern.asset');
    
    // Enhanced docs route
Route::get('/api/docs/{documentation?}', [SwaggerController::class, 'docs'])
        ->name('swagger.modern.docs')
        ->defaults('documentation', 'default');
        
    // Redirect default L5-Swagger route to our comprehensive documentation
    Route::get('/documentation', function () {
        return redirect('/api-docs-modern');
    })->name('swagger.redirect');
});

// Modern docs route targeting the full, dynamically generated OpenAPI schema
Route::get('/api-docs-modern', function () {
    $data = [
        'documentation' => 'default',
        'documentationTitle' => 'MNH API Documentation - Complete (265+ Endpoints)',
        // Point modern UI to the comprehensive OpenAPI JSON served by Api\SwaggerController@apiDocs
'urlsToDocs' => [
            'MNH API Documentation' => url('/api/docs'),
            'Raw OpenAPI JSON' => url('/api/docs')
        ],
        'useAbsolutePath' => true,
        'operationsSorter' => null,
        'configUrl' => null,
        'validatorUrl' => null,
    ];

    return view('l5-swagger::modern-index', $data);
})->name('swagger.modern');

// Test documentation JSON - removed; use unified /api-docs-modern which reads generated OpenAPI JSON
