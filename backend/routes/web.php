<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwaggerController;

Route::get('/', function () {
    return response()->json(['message' => 'Laravel API Server is running', 'timestamp' => now()]);
});

// Modern Swagger API Documentation Routes
// Alternative route for testing modern theme
Route::get('/api-docs-modern', function () {
    $data = [
        'documentation' => 'default',
        'documentationTitle' => 'MNH API Documentation',
        'urlsToDocs' => ['MNH API Documentation' => url('/api/test-docs.json')],
        'useAbsolutePath' => true,
        'operationsSorter' => null,
        'configUrl' => null,
        'validatorUrl' => null,
    ];

    return view('l5-swagger::modern-index', $data);
})->name('swagger.test');

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
                'description' => 'Health check and system status endpoints'
            ],
            [
                'name' => 'Auth',
                'description' => 'Authentication and session management endpoints'
            ],
            [
                'name' => 'Users',
                'description' => 'User management and administration endpoints'
            ],
            [
                'name' => 'Departments',
                'description' => 'Department management endpoints'
            ],
            [
                'name' => 'User Access',
                'description' => 'User access request management endpoints'
            ],
            [
                'name' => 'Booking Service',
                'description' => 'Device booking and reservation endpoints'
            ],
            [
                'name' => 'Onboarding',
                'description' => 'User onboarding process endpoints'
            ],
            [
                'name' => 'Declaration',
                'description' => 'Declaration form submission endpoints'
            ],
            [
                'name' => 'ICT Approval',
                'description' => 'ICT officer approval workflow endpoints'
            ],
            [
                'name' => 'HOD Dashboard',
                'description' => 'Head of Department dashboard and approval endpoints'
            ],
            [
                'name' => 'Head of IT',
                'description' => 'Head of IT management and task assignment endpoints'
            ],
            [
                'name' => 'ICT Officer',
                'description' => 'ICT officer task management and implementation endpoints'
            ],
            [
                'name' => 'Notifications',
                'description' => 'Notification and alert management endpoints'
            ],
            [
                'name' => 'Device Inventory',
                'description' => 'Device inventory and equipment management endpoints'
            ]
        ],
        'paths' => [
            // Health Check Endpoints
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
            '/api/health/detailed' => [
                'get' => [
                    'tags' => ['Health'],
                    'summary' => 'Detailed health check with database',
                    'description' => 'Check API health including database connection',
                    'responses' => [
                        '200' => [
                            'description' => 'Detailed health status'
                        ]
                    ]
                ]
            ],
            // Authentication Endpoints
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
            '/api/register' => [
                'post' => [
                    'tags' => ['Auth'],
                    'summary' => 'User registration',
                    'description' => 'Register a new user account',
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
                            'description' => 'Registration successful'
                        ],
                        '422' => [
                            'description' => 'Validation errors'
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
            '/api/logout-all' => [
                'post' => [
                    'tags' => ['Auth'],
                    'summary' => 'Logout all sessions',
                    'description' => 'Logout from all active sessions',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'All sessions logged out'
                        ]
                    ]
                ]
            ],
            '/api/sessions' => [
                'get' => [
                    'tags' => ['Auth'],
                    'summary' => 'List active sessions',
                    'description' => 'Get all active user sessions',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Active sessions list'
                        ]
                    ]
                ]
            ],
            '/api/current-user' => [
                'get' => [
                    'tags' => ['Auth'],
                    'summary' => 'Get current user',
                    'description' => 'Get authenticated user information',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Current user data'
                        ]
                    ]
                ]
            ],
            // User Management Endpoints  
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
            ],

            // User Access Request Endpoints
            '/api/v1/user-access' => [
                'get' => [
                    'tags' => ['User Access'],
                    'summary' => 'List user access requests',
                    'description' => 'Get paginated list of user access requests',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Access requests retrieved successfully'
                        ]
                    ]
                ],
                'post' => [
                    'tags' => ['User Access'],
                    'summary' => 'Create user access request',
                    'description' => 'Submit a new user access request',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['staff_name', 'department_id'],
                                    'properties' => [
                                        'staff_name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'department_id' => ['type' => 'integer', 'example' => 1],
                                        'pf_number' => ['type' => 'string', 'example' => 'PF001'],
                                        'request_type' => ['type' => 'string', 'example' => 'new_user']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Access request created successfully'
                        ]
                    ]
                ]
            ],

            // Booking Service Endpoints
            '/api/booking-service/bookings' => [
                'get' => [
                    'tags' => ['Booking Service'],
                    'summary' => 'List device bookings',
                    'description' => 'Get list of device booking requests',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Bookings retrieved successfully'
                        ]
                    ]
                ],
                'post' => [
                    'tags' => ['Booking Service'],
                    'summary' => 'Create device booking',
                    'description' => 'Submit a new device booking request',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['device_inventory_id', 'reason', 'duration_days'],
                                    'properties' => [
                                        'device_inventory_id' => ['type' => 'integer', 'example' => 1],
                                        'reason' => ['type' => 'string', 'example' => 'Project work'],
                                        'duration_days' => ['type' => 'integer', 'example' => 7]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Booking request created successfully'
                        ]
                    ]
                ]
            ],

            // Onboarding Endpoints
            '/api/onboarding/status' => [
                'get' => [
                    'tags' => ['Onboarding'],
                    'summary' => 'Get onboarding status',
                    'description' => 'Check current user onboarding progress',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Onboarding status retrieved'
                        ]
                    ]
                ]
            ],
            '/api/onboarding/complete' => [
                'post' => [
                    'tags' => ['Onboarding'],
                    'summary' => 'Complete onboarding',
                    'description' => 'Mark user onboarding as complete',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Onboarding completed successfully'
                        ]
                    ]
                ]
            ],

            // Declaration Endpoints
            '/api/declaration/submit' => [
                'post' => [
                    'tags' => ['Declaration'],
                    'summary' => 'Submit declaration form',
                    'description' => 'Submit user declaration form',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['declaration_accepted'],
                                    'properties' => [
                                        'declaration_accepted' => ['type' => 'boolean', 'example' => true],
                                        'terms_accepted' => ['type' => 'boolean', 'example' => true]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Declaration submitted successfully'
                        ]
                    ]
                ]
            ],

            // ICT Approval Endpoints
            '/api/ict-approval/device-requests' => [
                'get' => [
                    'tags' => ['ICT Approval'],
                    'summary' => 'List device requests for ICT approval',
                    'description' => 'Get device requests pending ICT officer approval',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Device requests retrieved successfully'
                        ]
                    ]
                ]
            ],
            '/api/ict-approval/device-requests/{requestId}/approve' => [
                'post' => [
                    'tags' => ['ICT Approval'],
                    'summary' => 'Approve device request',
                    'description' => 'Approve a device booking request',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Request approved successfully'
                        ]
                    ]
                ]
            ],

            // HOD Dashboard Endpoints
            '/api/both-service-form' => [
                'get' => [
                    'tags' => ['HOD Dashboard'],
                    'summary' => 'List service forms for HOD',
                    'description' => 'Get service forms for HOD approval',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Service forms retrieved successfully'
                        ]
                    ]
                ]
            ],
            '/api/both-service-form/{id}/hod-submit' => [
                'post' => [
                    'tags' => ['HOD Dashboard'],
                    'summary' => 'HOD submit form to next stage',
                    'description' => 'Submit form to next approval stage',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Form submitted to next stage'
                        ]
                    ]
                ]
            ],

            // Head of IT Endpoints
            '/api/head-of-it/all-requests' => [
                'get' => [
                    'tags' => ['Head of IT'],
                    'summary' => 'Get all requests for Head of IT',
                    'description' => 'List all requests that have reached Head of IT stage',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Requests retrieved successfully'
                        ]
                    ]
                ]
            ],
            '/api/head-of-it/assign-task' => [
                'post' => [
                    'tags' => ['Head of IT'],
                    'summary' => 'Assign task to ICT officer',
                    'description' => 'Assign implementation task to an ICT officer',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['request_id', 'ict_officer_id'],
                                    'properties' => [
                                        'request_id' => ['type' => 'integer', 'example' => 1],
                                        'ict_officer_id' => ['type' => 'integer', 'example' => 2],
                                        'instructions' => ['type' => 'string', 'example' => 'Please implement user access']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Task assigned successfully'
                        ]
                    ]
                ]
            ],

            // ICT Officer Endpoints
            '/api/ict-officer/access-requests' => [
                'get' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Get assigned access requests',
                    'description' => 'List access requests assigned to ICT officer',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Access requests retrieved successfully'
                        ]
                    ]
                ]
            ],
            '/api/ict-officer/access-requests/{requestId}/grant-access' => [
                'post' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Grant user access',
                    'description' => 'Grant access and complete the request',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Access granted successfully'
                        ]
                    ]
                ]
            ],

            // Notification Endpoints
            '/api/notifications/pending-count' => [
                'get' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Get pending notifications count',
                    'description' => 'Get count of pending requests for current user role',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Pending count retrieved successfully'
                        ]
                    ]
                ]
            ],

            // Device Inventory Endpoints
            '/api/device-inventory' => [
                'get' => [
                    'tags' => ['Device Inventory'],
                    'summary' => 'List device inventory',
                    'description' => 'Get list of available devices for booking',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Device inventory retrieved successfully'
                        ]
                    ]
                ],
                'post' => [
                    'tags' => ['Device Inventory'],
                    'summary' => 'Add device to inventory',
                    'description' => 'Add a new device to the inventory',
                    'security' => [
                        ['bearerAuth' => []]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['device_name', 'device_type', 'total_quantity'],
                                    'properties' => [
                                        'device_name' => ['type' => 'string', 'example' => 'Dell Laptop'],
                                        'device_type' => ['type' => 'string', 'example' => 'laptop'],
                                        'total_quantity' => ['type' => 'integer', 'example' => 10]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Device added to inventory successfully'
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
Route::get('/debug-swagger-urls', [App\Http\Controllers\DebugSwaggerController::class, 'debugUrls'])->name('debug.swagger.urls');
