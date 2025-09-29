<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SwaggerController extends Controller
{
    /**
     * Generate and serve Swagger documentation
     */
    public function documentation(Request $request)
    {
        // Always use our custom implementation since l5-swagger package isn't installed
        return $this->generateBasicSwaggerUI();
    }

    /**
     * Simple test endpoint to verify the controller is working
     */
    public function test()
    {
        return response()->json([
            'message' => 'SwaggerController is working!',
            'timestamp' => now()->toISOString(),
            'documentation_url' => url('/api/documentation'),
            'api_docs_url' => url('/api/api-docs'),
            'postman_collection_url' => url('/api/postman-collection')
        ]);
    }

    /**
     * Generate Postman Collection JSON
     */
    public function postmanCollection()
    {
        $apiDoc = $this->generateApiDocsStructure();
        
        $collection = [
            'info' => [
                'name' => $apiDoc['info']['title'],
                'description' => $apiDoc['info']['description'],
                'version' => $apiDoc['info']['version'],
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            ],
            'auth' => [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => '{{auth_token}}',
                        'type' => 'string'
                    ]
                ]
            ],
            'variable' => [
                [
                    'key' => 'base_url',
                    'value' => url('/api'),
                    'type' => 'string'
                ],
                [
                    'key' => 'auth_token',
                    'value' => 'your-token-here',
                    'type' => 'string'
                ]
            ],
            'item' => []
        ];

        // Convert OpenAPI paths to Postman requests
        foreach ($apiDoc['paths'] as $path => $methods) {
            foreach ($methods as $method => $details) {
                $item = [
                    'name' => $details['summary'] ?? ucfirst($method) . ' ' . $path,
                    'request' => [
                        'method' => strtoupper($method),
                        'header' => [
                            [
                                'key' => 'Content-Type',
                                'value' => 'application/json',
                                'type' => 'text'
                            ]
                        ],
                        'url' => [
                            'raw' => '{{base_url}}' . $path,
                            'host' => ['{{base_url}}'],
                            'path' => array_filter(explode('/', $path)),
                            'variable' => []
                        ],
                        'description' => $details['description'] ?? ''
                    ],
                    'response' => []
                ];

                // Add path parameters
                if (isset($details['parameters'])) {
                    foreach ($details['parameters'] as $param) {
                        if ($param['in'] === 'path') {
                            $item['request']['url']['variable'][] = [
                                'key' => $param['name'],
                                'value' => 'example_value'
                            ];
                        } elseif ($param['in'] === 'query') {
                            $item['request']['url']['query'][] = [
                                'key' => $param['name'],
                                'value' => $param['schema']['example'] ?? 'example_value',
                                'disabled' => true
                            ];
                        }
                    }
                }

                // Add request body
                if (isset($details['requestBody'])) {
                    $contentType = array_keys($details['requestBody']['content'])[0];
                    $schema = $details['requestBody']['content'][$contentType]['schema'] ?? [];
                    
                    if ($contentType === 'application/json' && isset($schema['properties'])) {
                        $body = [];
                        foreach ($schema['properties'] as $prop => $propDetails) {
                            $body[$prop] = $propDetails['example'] ?? 'example_value';
                        }
                        $item['request']['body'] = [
                            'mode' => 'raw',
                            'raw' => json_encode($body, JSON_PRETTY_PRINT),
                            'options' => [
                                'raw' => [
                                    'language' => 'json'
                                ]
                            ]
                        ];
                    }
                }

                $collection['item'][] = $item;
            }
        }

        return response()->json($collection, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="user-access-api.postman_collection.json"'
        ]);
    }

    /**
     * Extract API documentation structure for reuse
     */
    private function generateApiDocsStructure()
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'User Access Management API',
                'version' => '1.0.0',
                'description' => 'Laravel API for User Access Management, Module Requests, Device Booking and Workflow Approvals',
                'contact' => [
                    'name' => 'API Support',
                    'email' => 'admin@example.com'
                ]
            ],
            'paths' => $this->generatePaths()
        ];
    }

    /**
     * Generate API documentation JSON
     */
    public function apiDocs()
    {
        $apiDoc = array_merge($this->generateApiDocsStructure(), [
            'servers' => [
                [
                    'url' => url('/api'),
                    'description' => 'API Server'
                ]
            ],
            'components' => [
                'securitySchemes' => [
                    'sanctum' => [
                        'type' => 'apiKey',
                        'in' => 'header',
                        'name' => 'Authorization',
                        'description' => 'Enter token in format (Bearer <token>)'
                    ]
                ]
            ],
            'security' => [
                ['sanctum' => []]
            ],
            'tags' => [
                ['name' => 'Authentication', 'description' => 'User authentication and authorization endpoints'],
                ['name' => 'User Access', 'description' => 'User access request management'],
                ['name' => 'Both Service Form', 'description' => 'Combined service forms for multi-level approval workflow'],
                ['name' => 'Module Requests', 'description' => 'Wellsoft and Jeeva module access requests'],
                ['name' => 'Device Booking', 'description' => 'ICT device booking and management'],
                ['name' => 'ICT Approval', 'description' => 'ICT officer approval processes'],
                ['name' => 'ICT Officer', 'description' => 'ICT Officer task management and implementation workflow'],
                ['name' => 'Head of IT', 'description' => 'Head of IT approval and ICT Officer management'],
                ['name' => 'User Access Workflow', 'description' => 'Complete user access workflow with multi-level approvals'],
                ['name' => 'Admin', 'description' => 'Administrative functions (user and department management)'],
                ['name' => 'HOD Workflow', 'description' => 'Head of Department approval workflow'],
                ['name' => 'Divisional Workflow', 'description' => 'Divisional Director approval workflow'],
                ['name' => 'ICT Director Workflow', 'description' => 'ICT Director approval workflow'],
                ['name' => 'Profile', 'description' => 'User profile management and auto-population'],
                ['name' => 'Notifications', 'description' => 'User notification and pending request management'],
                ['name' => 'Utility', 'description' => 'Health checks and system utilities'],
                ['name' => 'Dashboard', 'description' => 'Dashboard and statistics endpoints']
            ]
        ]);

        return response()->json($apiDoc, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Generate comprehensive paths from all documented controllers
     */
    private function generatePaths()
    {
        return array_merge(
            $this->getAuthenticationPaths(),
            $this->getUserAccessPaths(),
            $this->getDeviceBookingPaths(),
            $this->getAdminPaths(),
            $this->getWorkflowPaths(),
            $this->getIctOfficerPaths(),
            $this->getHeadOfItPaths(),
            $this->getUserAccessWorkflowPaths(),
            $this->getModuleRequestPaths(),
            $this->getProfilePaths(),
            $this->getNotificationPaths(),
            $this->getUtilityPaths()
        );
    }

    /**
     * Authentication API paths
     */
    private function getAuthenticationPaths()
    {
        return [
            '/login' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'User Login',
                    'description' => 'Authenticate user and return access token',
                    'operationId' => 'login',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['email', 'password'],
                                    'properties' => [
                                        'email' => [
                                            'type' => 'string',
                                            'format' => 'email',
                                            'example' => 'user@example.com'
                                        ],
                                        'password' => [
                                            'type' => 'string',
                                            'format' => 'password',
                                            'example' => 'password123'
                                        ]
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
                                            'user' => ['type' => 'object'],
                                            'token' => ['type' => 'string'],
                                            'token_name' => ['type' => 'string']
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
            '/register' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'User Registration',
                    'description' => 'Register a new user account',
                    'operationId' => 'register',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'email', 'password', 'password_confirmation'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'john@example.com'],
                                        'password' => ['type' => 'string', 'format' => 'password'],
                                        'password_confirmation' => ['type' => 'string', 'format' => 'password']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'User registered successfully'],
                        '422' => ['description' => 'Validation errors']
                    ]
                ]
            ],
            '/logout' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'User Logout',
                    'description' => 'Logout user from current session',
                    'operationId' => 'logout',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Logout successful'],
                        '401' => ['description' => 'Unauthorized']
                    ]
                ]
            ],
            '/logout-all' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Logout from all sessions',
                    'description' => 'Logout user from all active sessions',
                    'operationId' => 'logoutAll',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Logged out from all sessions successfully'],
                        '401' => ['description' => 'Unauthorized']
                    ]
                ]
            ]
        ];
    }

    /**
     * User Access API paths
     */
    private function getUserAccessPaths()
    {
        return [
            '/v1/user-access' => [
                'get' => [
                    'tags' => ['User Access'],
                    'summary' => 'Get User Access Requests',
                    'description' => 'Retrieve paginated list of user access requests',
                    'operationId' => 'getUserAccessRequests',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filter by status',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['pending', 'approved', 'rejected', 'completed']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Success']
                    ]
                ],
                'post' => [
                    'tags' => ['User Access'],
                    'summary' => 'Create User Access Request',
                    'description' => 'Submit new user access request with digital signature',
                    'operationId' => 'createUserAccessRequest',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['pf_number', 'staff_name', 'phone_number', 'department_id', 'signature', 'request_type'],
                                    'properties' => [
                                        'pf_number' => ['type' => 'string', 'example' => 'PF12345'],
                                        'staff_name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'phone_number' => ['type' => 'string', 'example' => '+1234567890'],
                                        'department_id' => ['type' => 'integer', 'example' => 1],
                                        'signature' => ['type' => 'string', 'format' => 'binary'],
                                        'request_type' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'string',
                                                'enum' => ['wellsoft_access', 'jeeva_access', 'internet_access_request']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'User access request created successfully']
                    ]
                ]
            ],
            '/both-service-form' => [
                'get' => [
                    'tags' => ['Both Service Form'],
                    'summary' => 'Get Both Service Forms',
                    'description' => 'Retrieve list of combined service forms for HOD/Divisional/ICT approval workflow',
                    'operationId' => 'getBothServiceForms',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Forms retrieved successfully']
                    ]
                ],
                'post' => [
                    'tags' => ['Both Service Form'],
                    'summary' => 'Create Both Service Form',
                    'description' => 'Create new combined service form with module requests',
                    'operationId' => 'createBothServiceForm',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['shared', 'approvals', 'request_types'],
                                    'properties' => [
                                        'shared' => ['type' => 'object'],
                                        'approvals' => ['type' => 'object'],
                                        'request_types' => ['type' => 'array', 'items' => ['type' => 'string']]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Form created successfully']
                    ]
                ]
            ]
        ];
    }

    /**
     * Device Booking API paths
     */
    private function getDeviceBookingPaths()
    {
        return [
            '/booking-service/bookings' => [
                'get' => [
                    'tags' => ['Device Booking'],
                    'summary' => 'Get Device Bookings',
                    'description' => 'Retrieve user device booking requests',
                    'operationId' => 'getDeviceBookings',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filter by booking status',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['pending', 'approved', 'rejected', 'completed']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Device bookings retrieved successfully']
                    ]
                ],
                'post' => [
                    'tags' => ['Device Booking'],
                    'summary' => 'Create Device Booking',
                    'description' => 'Submit new device booking request',
                    'operationId' => 'createDeviceBooking',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['device_inventory_id', 'booking_date', 'return_date', 'purpose'],
                                    'properties' => [
                                        'device_inventory_id' => ['type' => 'integer', 'example' => 1],
                                        'device_type' => ['type' => 'string', 'example' => 'Laptop'],
                                        'booking_date' => ['type' => 'string', 'format' => 'date', 'example' => '2024-01-15'],
                                        'return_date' => ['type' => 'string', 'format' => 'date', 'example' => '2024-01-20'],
                                        'purpose' => ['type' => 'string', 'example' => 'Official work']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Device booking created successfully']
                    ]
                ]
            ],
            '/ict-approval/device-requests' => [
                'get' => [
                    'tags' => ['ICT Approval'],
                    'summary' => 'Get Device Borrowing Requests for ICT Approval',
                    'description' => 'Retrieve device borrowing requests pending ICT officer approval',
                    'operationId' => 'getDeviceBorrowingRequests',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filter by status',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['pending', 'ict_approved', 'approved', 'rejected', 'completed']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Device borrowing requests retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - ICT officer access required']
                    ]
                ]
            ]
        ];
    }

    /**
     * Admin API paths
     */
    private function getAdminPaths()
    {
        return [
            '/admin/users' => [
                'get' => [
                    'tags' => ['Admin'],
                    'summary' => 'Get All Users',
                    'description' => 'Retrieve paginated list of all users (Admin only)',
                    'operationId' => 'getAllUsers',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'per_page',
                            'in' => 'query',
                            'description' => 'Items per page',
                            'schema' => ['type' => 'integer', 'default' => 15]
                        ],
                        [
                            'name' => 'search',
                            'in' => 'query',
                            'description' => 'Search users by name, email, or PF number',
                            'schema' => ['type' => 'string']
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Users retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - Admin access required']
                    ]
                ],
                'post' => [
                    'tags' => ['Admin'],
                    'summary' => 'Create New User',
                    'description' => 'Create a new user account (Admin only)',
                    'operationId' => 'createUser',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'email', 'password', 'pf_number'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'john@example.com'],
                                        'password' => ['type' => 'string', 'format' => 'password'],
                                        'pf_number' => ['type' => 'string', 'example' => 'PF12345'],
                                        'phone' => ['type' => 'string'],
                                        'department_id' => ['type' => 'integer'],
                                        'roles' => ['type' => 'array', 'items' => ['type' => 'string']]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'User created successfully'],
                        '403' => ['description' => 'Unauthorized - Admin access required']
                    ]
                ]
            ],
            '/admin/departments' => [
                'get' => [
                    'tags' => ['Admin'],
                    'summary' => 'Get All Departments',
                    'description' => 'Retrieve all departments (Admin only)',
                    'operationId' => 'getAllDepartments',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Departments retrieved successfully']
                    ]
                ],
                'post' => [
                    'tags' => ['Admin'],
                    'summary' => 'Create Department',
                    'description' => 'Create a new department (Admin only)',
                    'operationId' => 'createDepartment',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'code'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Information Technology'],
                                        'code' => ['type' => 'string', 'example' => 'IT'],
                                        'description' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Department created successfully']
                    ]
                ]
            ]
        ];
    }

    /**
     * Workflow API paths (HOD, Divisional, ICT Director approvals)
     */
    private function getWorkflowPaths()
    {
        return [
            '/hod/combined-access-requests' => [
                'get' => [
                    'tags' => ['HOD Workflow'],
                    'summary' => 'Get HOD Combined Access Requests',
                    'description' => 'Retrieve requests pending HOD approval',
                    'operationId' => 'getHodRequests',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'HOD requests retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - HOD access required']
                    ]
                ]
            ],
            '/divisional/combined-access-requests' => [
                'get' => [
                    'tags' => ['Divisional Workflow'],
                    'summary' => 'Get Divisional Director Requests',
                    'description' => 'Retrieve requests pending Divisional Director approval',
                    'operationId' => 'getDivisionalRequests',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Divisional requests retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - Divisional Director access required']
                    ]
                ]
            ],
            '/dict/combined-access-requests' => [
                'get' => [
                    'tags' => ['ICT Director Workflow'],
                    'summary' => 'Get ICT Director Requests',
                    'description' => 'Retrieve requests pending ICT Director approval',
                    'operationId' => 'getIctDirectorRequests',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'ICT Director requests retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - ICT Director access required']
                    ]
                ]
            ]
        ];
    }

    /**
     * Profile API paths
     */
    private function getProfilePaths()
    {
        return [
            '/user' => [
                'get' => [
                    'tags' => ['Profile'],
                    'summary' => 'Get Current User Profile',
                    'description' => 'Retrieve current authenticated user profile information',
                    'operationId' => 'getCurrentUser',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'User profile retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 1],
                                            'name' => ['type' => 'string', 'example' => 'John Doe'],
                                            'email' => ['type' => 'string', 'example' => 'john@example.com'],
                                            'phone' => ['type' => 'string', 'example' => '+1234567890'],
                                            'pf_number' => ['type' => 'string', 'example' => 'PF12345'],
                                            'role' => ['type' => 'string', 'example' => 'staff'],
                                            'roles' => ['type' => 'array', 'items' => ['type' => 'string']],
                                            'department' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => ['type' => 'integer'],
                                                    'name' => ['type' => 'string'],
                                                    'code' => ['type' => 'string']
                                                ]
                                            ],
                                            'needs_onboarding' => ['type' => 'boolean']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '/profile/current' => [
                'get' => [
                    'tags' => ['Profile'],
                    'summary' => 'Get Extended User Profile',
                    'description' => 'Get detailed current user profile for form auto-population',
                    'operationId' => 'getExtendedProfile',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Extended profile retrieved successfully']
                    ]
                ],
                'put' => [
                    'tags' => ['Profile'],
                    'summary' => 'Update User Profile',
                    'description' => 'Update current user profile information',
                    'operationId' => 'updateProfile',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'phone' => ['type' => 'string'],
                                        'staff_name' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Profile updated successfully']
                    ]
                ]
            ]
        ];
    }

    /**
     * Utility API paths (health, notifications, etc.)
     */
    private function getUtilityPaths()
    {
        return [
            '/health' => [
                'get' => [
                    'tags' => ['Utility'],
                    'summary' => 'API Health Check',
                    'description' => 'Check API health status',
                    'operationId' => 'healthCheck',
                    'responses' => [
                        '200' => [
                            'description' => 'API is healthy',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'status' => ['type' => 'string', 'example' => 'ok'],
                                            'timestamp' => ['type' => 'string', 'format' => 'date-time']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '/health/detailed' => [
                'get' => [
                    'tags' => ['Utility'],
                    'summary' => 'Detailed Health Check',
                    'description' => 'Get detailed API health status including database connection',
                    'operationId' => 'detailedHealthCheck',
                    'responses' => [
                        '200' => [
                            'description' => 'Detailed health information',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'status' => ['type' => 'string'],
                                            'database' => ['type' => 'object'],
                                            'environment' => ['type' => 'string'],
                                            'php_version' => ['type' => 'string'],
                                            'laravel_version' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '/notifications' => [
                'get' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Get User Notifications',
                    'description' => 'Retrieve user notifications',
                    'operationId' => 'getNotifications',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Notifications retrieved successfully']
                    ]
                ]
            ],
            '/notifications/unread-count' => [
                'get' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Get Unread Notifications Count',
                    'description' => 'Get count of unread notifications for current user',
                    'operationId' => 'getUnreadCount',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Unread count retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'unread_count' => ['type' => 'integer', 'example' => 5]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * ICT Officer API paths
     */
    private function getIctOfficerPaths()
    {
        return [
            '/ict-officer/dashboard' => [
                'get' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Get ICT Officer Dashboard',
                    'description' => 'Retrieve dashboard data for ICT Officer with task statistics and assignments',
                    'operationId' => 'getIctOfficerDashboard',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Dashboard data retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean'],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'officer_info' => ['type' => 'object'],
                                                    'task_counts' => ['type' => 'object'],
                                                    'recent_assignments' => ['type' => 'array']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        '403' => ['description' => 'Unauthorized - ICT Officer access required']
                    ]
                ]
            ],
            '/ict-officer/access-requests' => [
                'get' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Get Access Requests for ICT Implementation',
                    'description' => 'Retrieve access requests approved by Head of IT and available for ICT Officer implementation',
                    'operationId' => 'getIctAccessRequests',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filter by implementation status',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['unassigned', 'assigned_to_ict', 'implementation_in_progress', 'completed']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Access requests retrieved successfully']
                    ]
                ]
            ],
            '/ict-officer/access-requests/{requestId}' => [
                'get' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Get Access Request Details',
                    'description' => 'Get detailed information about a specific access request',
                    'operationId' => 'getAccessRequestById',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Access request details retrieved successfully'],
                        '404' => ['description' => 'Access request not found']
                    ]
                ]
            ],
            '/ict-officer/access-requests/{requestId}/assign' => [
                'post' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Assign Access Request to Self',
                    'description' => 'ICT Officer takes ownership of an access request implementation',
                    'operationId' => 'assignAccessRequestToSelf',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'notes' => ['type' => 'string', 'maxLength' => 500, 'example' => 'Task self-assigned by ICT Officer']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Access request assigned successfully'],
                        '400' => ['description' => 'Request already assigned or invalid state']
                    ]
                ]
            ],
            '/ict-officer/access-requests/{requestId}/progress' => [
                'put' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Update Access Request Progress',
                    'description' => 'Update implementation progress on an assigned access request',
                    'operationId' => 'updateAccessRequestProgress',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
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
                                    'required' => ['status'],
                                    'properties' => [
                                        'status' => [
                                            'type' => 'string',
                                            'enum' => ['implementation_in_progress', 'completed'],
                                            'example' => 'implementation_in_progress'
                                        ],
                                        'notes' => ['type' => 'string', 'maxLength' => 1000, 'example' => 'Implementation started successfully']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Progress updated successfully'],
                        '400' => ['description' => 'Invalid status or request state']
                    ]
                ]
            ],
            '/ict-officer/access-requests/{requestId}/timeline' => [
                'get' => [
                    'tags' => ['ICT Officer'],
                    'summary' => 'Get Access Request Timeline',
                    'description' => 'Get detailed timeline and history for an access request',
                    'operationId' => 'getAccessRequestTimeline',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Timeline retrieved successfully'],
                        '404' => ['description' => 'Access request not found']
                    ]
                ]
            ]
        ];
    }

    /**
     * Head of IT API paths
     */
    private function getHeadOfItPaths()
    {
        return [
            '/head-of-it/all-requests' => [
                'get' => [
                    'tags' => ['Head of IT'],
                    'summary' => 'Get All Requests for Head of IT',
                    'description' => 'Retrieve all requests that have reached Head of IT approval stage',
                    'operationId' => 'getHeadOfItAllRequests',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filter by approval status',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['pending', 'approved', 'rejected']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Requests retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - Head of IT access required']
                    ]
                ]
            ],
            '/head-of-it/requests/{id}/approve' => [
                'post' => [
                    'tags' => ['Head of IT'],
                    'summary' => 'Approve Access Request',
                    'description' => 'Approve an access request as Head of IT',
                    'operationId' => 'approveAccessRequest',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
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
                                        'comments' => ['type' => 'string', 'example' => 'Request approved for implementation'],
                                        'signature' => ['type' => 'string', 'format' => 'binary']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Request approved successfully'],
                        '400' => ['description' => 'Invalid request state or data']
                    ]
                ]
            ],
            '/head-of-it/ict-officers' => [
                'get' => [
                    'tags' => ['Head of IT'],
                    'summary' => 'Get Available ICT Officers',
                    'description' => 'Retrieve list of available ICT Officers for task assignment',
                    'operationId' => 'getAvailableIctOfficers',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'ICT Officers list retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean'],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'id' => ['type' => 'integer'],
                                                        'name' => ['type' => 'string'],
                                                        'email' => ['type' => 'string'],
                                                        'workload' => ['type' => 'integer'],
                                                        'availability_status' => ['type' => 'string']
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * User Access Workflow API paths
     */
    private function getUserAccessWorkflowPaths()
    {
        return [
            '/user-access-workflow' => [
                'get' => [
                    'tags' => ['User Access Workflow'],
                    'summary' => 'Get User Access Workflow Requests',
                    'description' => 'Retrieve paginated list of user access workflow requests',
                    'operationId' => 'getUserAccessWorkflowRequests',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filter by workflow status',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['draft', 'hod_pending', 'divisional_pending', 'ict_director_pending', 'head_it_pending', 'implementation_pending', 'completed']
                            ]
                        ],
                        [
                            'name' => 'per_page',
                            'in' => 'query',
                            'description' => 'Items per page',
                            'schema' => ['type' => 'integer', 'default' => 15]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Workflow requests retrieved successfully']
                    ]
                ],
                'post' => [
                    'tags' => ['User Access Workflow'],
                    'summary' => 'Create User Access Workflow Request',
                    'description' => 'Submit new user access workflow request with complete approval chain',
                    'operationId' => 'createUserAccessWorkflowRequest',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['staff_name', 'pf_number', 'department_id', 'request_type'],
                                    'properties' => [
                                        'staff_name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'pf_number' => ['type' => 'string', 'example' => 'PF12345'],
                                        'phone_number' => ['type' => 'string'],
                                        'department_id' => ['type' => 'integer'],
                                        'request_type' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'string',
                                                'enum' => ['wellsoft_access', 'jeeva_access', 'internet_access_request']
                                            ]
                                        ],
                                        'access_type' => ['type' => 'string', 'enum' => ['permanent', 'temporary']],
                                        'temporary_until' => ['type' => 'string', 'format' => 'date'],
                                        'internet_purposes' => ['type' => 'string'],
                                        'wellsoft_modules_selected' => ['type' => 'array'],
                                        'jeeva_modules_selected' => ['type' => 'array'],
                                        'signature' => ['type' => 'string', 'format' => 'binary']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Workflow request created successfully'],
                        '422' => ['description' => 'Validation errors']
                    ]
                ]
            ],
            '/user-access-workflow/{userAccess}/approve/hod' => [
                'post' => [
                    'tags' => ['User Access Workflow'],
                    'summary' => 'HOD Approval',
                    'description' => 'Process Head of Department approval for access request',
                    'operationId' => 'processHodApproval',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'userAccess',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['action'],
                                    'properties' => [
                                        'action' => ['type' => 'string', 'enum' => ['approve', 'reject']],
                                        'comments' => ['type' => 'string'],
                                        'signature' => ['type' => 'string', 'format' => 'binary']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'HOD approval processed successfully'],
                        '403' => ['description' => 'Unauthorized - HOD access required']
                    ]
                ]
            ]
        ];
    }

    /**
     * Module Request API paths
     */
    private function getModuleRequestPaths()
    {
        return [
            '/module-requests' => [
                'post' => [
                    'tags' => ['Module Requests'],
                    'summary' => 'Create Module Access Request',
                    'description' => 'Submit request for access to specific Wellsoft or Jeeva modules',
                    'operationId' => 'createModuleRequest',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['user_access_id', 'module_type', 'modules'],
                                    'properties' => [
                                        'user_access_id' => ['type' => 'integer', 'example' => 1],
                                        'module_type' => ['type' => 'string', 'enum' => ['wellsoft', 'jeeva'], 'example' => 'wellsoft'],
                                        'modules' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                            'example' => ['Patient Registration', 'Billing']
                                        ],
                                        'justification' => ['type' => 'string', 'example' => 'Required for patient management duties']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Module request created successfully'],
                        '422' => ['description' => 'Validation errors']
                    ]
                ]
            ],
            '/module-requests/modules' => [
                'get' => [
                    'tags' => ['Module Requests'],
                    'summary' => 'Get Available Modules',
                    'description' => 'Retrieve list of available modules for Wellsoft and Jeeva systems',
                    'operationId' => 'getAvailableModules',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'type',
                            'in' => 'query',
                            'description' => 'Filter by module type',
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['wellsoft', 'jeeva']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Available modules retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'wellsoft_modules' => [
                                                'type' => 'array',
                                                'items' => ['type' => 'string']
                                            ],
                                            'jeeva_modules' => [
                                                'type' => 'array',
                                                'items' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Notification API paths
     */
    private function getNotificationPaths()
    {
        return [
            '/notifications/pending-count' => [
                'get' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Get Pending Requests Count',
                    'description' => 'Get count of pending requests for current user based on their role',
                    'operationId' => 'getPendingRequestsCount',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Pending count retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean'],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'total_pending' => ['type' => 'integer', 'example' => 5],
                                                    'by_status' => ['type' => 'object'],
                                                    'requires_attention' => ['type' => 'boolean']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            '/notifications/breakdown' => [
                'get' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Get Detailed Notification Breakdown',
                    'description' => 'Get detailed breakdown of pending requests across all roles (Admin only)',
                    'operationId' => 'getPendingRequestsBreakdown',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Notification breakdown retrieved successfully'],
                        '403' => ['description' => 'Unauthorized - Admin access required']
                    ]
                ]
            ]
        ];
    }

    /**
     * Generate interactive Swagger UI with testing capabilities
     */
    private function generateBasicSwaggerUI()
    {
        $apiDocsUrl = url('/api/api-docs');
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Access Management API - Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.10.3/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin: 0;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .swagger-ui .topbar {
            background-color: #1b1b1b;
        }
        .swagger-ui .topbar .download-url-wrapper {
            display: none;
        }
        .custom-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 0;
        }
        .custom-header h1 {
            margin: 0;
            font-size: 2em;
            font-weight: 300;
        }
        .custom-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .info-section {
            background: white;
            padding: 20px;
            margin: 0;
            border-bottom: 1px solid #e8e8e8;
        }
        .info-section h3 {
            color: #3b4151;
            margin-top: 0;
        }
        .auth-info {
            background: #f7f7f7;
            border-left: 4px solid #61affe;
            padding: 15px;
            margin: 10px 0;
        }
        .endpoints-count {
            background: #e8f5e8;
            border-left: 4px solid #4caf50;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
        }
        .download-section {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
        }
        .download-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #ff6c37;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px 5px 5px 0;
            font-size: 14px;
        }
        .download-btn:hover {
            background: #e55a2b;
            color: white;
            text-decoration: none;
        }
        /* Override Swagger UI styles */
        .swagger-ui .info {
            margin: 20px 0;
        }
        .swagger-ui .scheme-container {
            background: #fff;
            box-shadow: none;
            border: 1px solid #d3d3d3;
        }
    </style>
</head>
<body>
    <div class="custom-header">
        <h1>🚀 User Access Management API</h1>
        <p>Interactive API Documentation & Testing Interface</p>
        <p>Version 1.0.0 | Laravel Backend API</p>
    </div>
    
    <div class="info-section">
        <h3>🚀 Getting Started</h3>
        <p>This is an interactive API documentation where you can test all endpoints directly from your browser.</p>
        
        <div class="auth-info">
            <strong>🔐 Authentication Required:</strong><br>
            Most endpoints require authentication. Click the "Authorize" button below and enter your Bearer token:<br>
            <code>Bearer your-access-token-here</code><br><br>
            <strong>How to get a token:</strong> Use the <code>POST /login</code> endpoint with your credentials.
        </div>
        
        <div class="endpoints-count">
            📊 Total Endpoints: 50+ endpoints across 16 categories including Authentication, User Access, Device Booking, and Workflow Management
        </div>
        
        <div class="download-section">
            <strong>📦 Additional Resources:</strong><br>
            <a href="' . url('/api/postman-collection') . '" class="download-btn" download="user-access-api.postman_collection.json">Download Postman Collection</a>
            <a href="' . $apiDocsUrl . '" class="download-btn" target="_blank">View Raw OpenAPI JSON</a>
        </div>
        
        <p><strong>Base URL:</strong> <code>' . url('/api') . '</code></p>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.10.3/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.10.3/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            // Build Swagger UI
            const ui = SwaggerUIBundle({
                url: "' . $apiDocsUrl . '",
                dom_id: "#swagger-ui",
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                requestInterceptor: (request) => {
                    // Add custom headers if needed
                    request.headers["Accept"] = "application/json";
                    // Add CORS headers for local testing
                    request.headers["X-Requested-With"] = "XMLHttpRequest";
                    console.log("Making request to:", request.url);
                    return request;
                },
                responseInterceptor: (response) => {
                    // Log responses for debugging
                    console.log("API Response:", {
                        url: response.url,
                        status: response.status,
                        headers: response.headers
                    });
                    return response;
                },
                onComplete: () => {
                    console.log("Swagger UI loaded successfully");
                    
                    // Hide the default Swagger topbar
                    setTimeout(() => {
                        const topbar = document.querySelector(".swagger-ui .topbar");
                        if (topbar) {
                            topbar.style.display = "none";
                        }
                    }, 1000);
                },
                tryItOutEnabled: true,
                supportedSubmitMethods: ["get", "post", "put", "delete", "patch", "head", "options"],
                validatorUrl: null, // Disable online validation
                docExpansion: "list", // Can be "list", "full", or "none"
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                displayOperationId: false,
                displayRequestDuration: true,
                filter: true, // Enable endpoint filtering
                showExtensions: true,
                showCommonExtensions: true,
                persistAuthorization: true, // Keep authorization when page refreshes
                syntaxHighlight: {
                    activated: true,
                    theme: "agate"
                },
                requestSnippetsEnabled: true,
                requestSnippets: {
                    generators: {
                        "curl_bash": {
                            title: "cURL (bash)",
                            syntax: "bash"
                        },
                        "curl_powershell": {
                            title: "cURL (PowerShell)",
                            syntax: "powershell"
                        },
                        "curl_cmd": {
                            title: "cURL (CMD)",
                            syntax: "bash"
                        }
                    },
                    defaultExpanded: false,
                    languages: ["curl_bash", "curl_powershell", "curl_cmd"]
                }
            });
            
            // Additional customization
            setTimeout(() => {
                // Add custom CSS for better mobile experience
                const style = document.createElement("style");
                style.textContent = `
                    @media (max-width: 768px) {
                        .swagger-ui .wrapper {
                            padding: 0 10px;
                        }
                        .custom-header {
                            padding: 15px;
                        }
                        .custom-header h1 {
                            font-size: 1.5em;
                        }
                    }
                `;
                document.head.appendChild(style);
            }, 1500);
        };
    </script>
</body>
</html>';

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
