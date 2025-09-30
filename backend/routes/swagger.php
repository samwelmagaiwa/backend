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
    // Modern API Documentation Route
    Route::get('api/documentation', [SwaggerController::class, 'api'])->name('swagger.modern.api');
    
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
            'MNH API Documentation' => url('/api/api-docs'),
            'Raw OpenAPI JSON' => url('/api/api-docs')
        ],
        'useAbsolutePath' => true,
        'operationsSorter' => null,
        'configUrl' => null,
        'validatorUrl' => null,
    ];

    return view('l5-swagger::modern-index', $data);
})->name('swagger.modern');

// Test documentation JSON
Route::get('/api/test-docs.json', function () {
    $testDocs = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'MNH API Documentation',
            'description' => 'Modern API documentation for authentication, user management, departments, roles, onboarding, declarations, booking services, and combined service forms.',
            'version' => '1.0.0',
            'contact' => [
                'name' => 'API Support',
                'email' => 'support@example.com'
            ]
        ],
        'servers' => [
            [
                'url' => config('app.url', 'http://localhost:8000'),
                'description' => 'Development Server'
            ]
        ],
        'tags' => [
            [
                'name' => 'Health',
                'description' => 'Health check endpoints'
            ],
            [
                'name' => 'Auth',
                'description' => 'Authentication endpoints'
            ],
            [
                'name' => 'Users',
                'description' => 'User management endpoints'
            ],
            [
                'name' => 'Departments',
                'description' => 'Department management endpoints'
            ]
        ],
        'paths' => [
            '/api/health' => [
                'get' => [
                    'tags' => ['Health'],
                    'summary' => 'Basic health check',
                    'description' => 'Check if the API is running',
                    'responses' => [
                        '200' => [
                            'description' => 'API is healthy',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'status' => ['type' => 'string', 'example' => 'ok'],
                                            'timestamp' => ['type' => 'string', 'example' => '2024-01-01 12:00:00']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '/api/login' => [
                'post' => [
                    'tags' => ['Auth'],
                    'summary' => 'User login',
                    'description' => 'Authenticate a user and return a token',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['email', 'password'],
                                    'properties' => [
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'user@example.com'],
                                        'password' => ['type' => 'string', 'example' => 'password123']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Login successful',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'token' => ['type' => 'string'],
                                            'user' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => ['type' => 'integer'],
                                                    'email' => ['type' => 'string'],
                                                    'name' => ['type' => 'string']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        '401' => [
                            'description' => 'Invalid credentials'
                        ]
                    ]
                ]
            ],
            '/api/logout' => [
                'post' => [
                    'tags' => ['Auth'],
                    'summary' => 'User logout',
                    'description' => 'Logout the current user',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Logout successful'
                        ],
                        '401' => [
                            'description' => 'Unauthorized'
                        ]
                    ]
                ]
            ],
            '/api/admin/users' => [
                'get' => [
                    'tags' => ['Users'],
                    'summary' => 'List users',
                    'description' => 'Get a paginated list of users',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'search',
                            'in' => 'query',
                            'description' => 'Search term',
                            'required' => false,
                            'schema' => ['type' => 'string']
                        ],
                        [
                            'name' => 'page',
                            'in' => 'query',
                            'description' => 'Page number',
                            'required' => false,
                            'schema' => ['type' => 'integer', 'default' => 1]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Users list retrieved successfully'
                        ]
                    ]
                ],
                'post' => [
                    'tags' => ['Users'],
                    'summary' => 'Create user',
                    'description' => 'Create a new user',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'email', 'password'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'john@example.com'],
                                        'password' => ['type' => 'string', 'example' => 'password123']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'User created successfully'
                        ],
                        '422' => [
                            'description' => 'Validation error'
                        ]
                    ]
                ]
            ],
            '/api/admin/users/{id}' => [
                'get' => [
                    'tags' => ['Users'],
                    'summary' => 'Get user',
                    'description' => 'Get a specific user by ID',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'description' => 'User ID',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'User retrieved successfully'
                        ],
                        '404' => [
                            'description' => 'User not found'
                        ]
                    ]
                ],
                'put' => [
                    'tags' => ['Users'],
                    'summary' => 'Update user',
                    'description' => 'Update a specific user',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'description' => 'User ID',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'email' => ['type' => 'string', 'format' => 'email'],
                                        'password' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'User updated successfully'
                        ],
                        '404' => [
                            'description' => 'User not found'
                        ],
                        '422' => [
                            'description' => 'Validation error'
                        ]
                    ]
                ],
                'delete' => [
                    'tags' => ['Users'],
                    'summary' => 'Delete user',
                    'description' => 'Delete a specific user',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'description' => 'User ID',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'User deleted successfully'
                        ],
                        '404' => [
                            'description' => 'User not found'
                        ]
                    ]
                ]
            ],
            '/api/admin/departments' => [
                'get' => [
                    'tags' => ['Departments'],
                    'summary' => 'List departments',
                    'description' => 'Get all departments',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Departments list retrieved successfully'
                        ]
                    ]
                ],
                'post' => [
                    'tags' => ['Departments'],
                    'summary' => 'Create department',
                    'description' => 'Create a new department',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'IT Department'],
                                        'description' => ['type' => 'string', 'example' => 'Information Technology Department']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Department created successfully'
                        ],
                        '422' => [
                            'description' => 'Validation error'
                        ]
                    ]
                ]
            ]
        ],
        'components' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                    'description' => 'JWT Authorization header using the Bearer scheme.'
                ]
            ]
        ]
    ];

    return response()->json($testDocs)
        ->header('Access-Control-Allow-Origin', '*');
});
