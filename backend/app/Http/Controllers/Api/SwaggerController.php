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
            'api_docs_url' => url('/api/api-docs')
        ]);
    }

    /**
     * Generate API documentation JSON
     */
    public function apiDocs()
    {
        // Generate basic API documentation structure
        $apiDoc = [
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
                ['name' => 'Admin', 'description' => 'Administrative functions (user and department management)'],
                ['name' => 'HOD Workflow', 'description' => 'Head of Department approval workflow'],
                ['name' => 'Divisional Workflow', 'description' => 'Divisional Director approval workflow'],
                ['name' => 'ICT Director Workflow', 'description' => 'ICT Director approval workflow'],
                ['name' => 'Profile', 'description' => 'User profile management and auto-population'],
                ['name' => 'Notifications', 'description' => 'User notification management'],
                ['name' => 'Utility', 'description' => 'Health checks and system utilities'],
                ['name' => 'Dashboard', 'description' => 'Dashboard and statistics endpoints']
            ],
            'paths' => $this->generatePaths()
        ];

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
            $this->getProfilePaths(),
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
     * Generate basic Swagger UI HTML
     */
    private function generateBasicSwaggerUI()
    {
        $apiDocsUrl = url('/api/api-docs');
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Access Management API - Documentation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .content {
            padding: 30px;
        }
        .endpoints {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .endpoint {
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .endpoint:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .endpoint-header {
            padding: 15px;
            font-weight: 600;
            color: white;
        }
        .endpoint-body {
            padding: 15px;
            background: #f8f9fa;
        }
        .method-get { background: #28a745; }
        .method-post { background: #007bff; }
        .method-put { background: #ffc107; color: #212529; }
        .method-delete { background: #dc3545; }
        .code {
            background: #f1f3f4;
            padding: 10px;
            border-radius: 4px;
            font-family: "Monaco", "Menlo", monospace;
            font-size: 0.9em;
            margin: 10px 0;
            overflow-x: auto;
        }
        .json-viewer {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 6px;
            font-family: "Monaco", "Menlo", monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .test-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 20px;
        }
        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .nav-tabs li {
            margin-bottom: -1px;
        }
        .nav-tabs button {
            padding: 10px 20px;
            border: 1px solid transparent;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            cursor: pointer;
            font-size: 14px;
        }
        .nav-tabs button.active {
            background: white;
            border-color: #dee2e6 #dee2e6 white;
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 User Access Management API</h1>
            <p>Complete API Documentation & Testing Interface</p>
            <p><strong>Version:</strong> 1.0.0 | <strong>Status:</strong> <span id="api-status">Checking...</span></p>
        </div>
        
        <div class="content">
            <div class="alert alert-info">
                <strong>📚 Interactive API Documentation</strong><br>
                This documentation provides comprehensive information about all available API endpoints. 
                Use the testing interface below to explore and test the API endpoints directly.
            </div>

            <ul class="nav-tabs">
                <li><button class="active" onclick="showTab(event, \'overview\');">Overview</button></li>
                <li><button onclick="showTab(event, \'endpoints\');">Endpoints</button></li>
                <li><button onclick="showTab(event, \'testing\');">API Testing</button></li>
                <li><button onclick="showTab(event, \'schema\');">JSON Schema</button></li>
            </ul>

            <div id="overview" class="tab-pane active">
                <h2>🔐 Authentication</h2>
                <p>This API uses <strong>Laravel Sanctum</strong> for authentication. Include your token in the Authorization header:</p>
                <div class="code">Authorization: Bearer {your-token-here}</div>
                
                <h3>Getting an Access Token</h3>
                <div class="code">POST ' . url('/api/login') . '
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "your-password"
}</div>
                
                <h2>📋 Key Features</h2>
                <ul>
                    <li><strong>User Access Requests:</strong> Submit and manage access requests for Wellsoft, Jeeva, and Internet services</li>
                    <li><strong>Device Booking:</strong> Reserve and manage ICT devices with approval workflows</li>
                    <li><strong>Workflow Management:</strong> Multi-level approval processes with role-based access</li>
                    <li><strong>Administrative Tools:</strong> User management, department administration, and system reporting</li>
                </ul>
            </div>

            <div id="endpoints" class="tab-pane">
                <h2>🌐 Available Endpoints</h2>
                <div class="endpoints">
                    <div class="endpoint">
                        <div class="endpoint-header method-post">POST /api/login</div>
                        <div class="endpoint-body">
                            <strong>User Authentication</strong><br>
                            Authenticate user and return access token
                        </div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-header method-get">GET /api/user</div>
                        <div class="endpoint-body">
                            <strong>Current User Profile</strong><br>
                            Get authenticated user information
                        </div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-header method-get">GET /api/v1/user-access</div>
                        <div class="endpoint-body">
                            <strong>User Access Requests</strong><br>
                            List paginated user access requests
                        </div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-header method-post">POST /api/v1/user-access</div>
                        <div class="endpoint-body">
                            <strong>Create Access Request</strong><br>
                            Submit new user access request with digital signature
                        </div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-header method-get">GET /api/booking-service/bookings</div>
                        <div class="endpoint-body">
                            <strong>Device Bookings</strong><br>
                            List device booking requests
                        </div>
                    </div>
                    <div class="endpoint">
                        <div class="endpoint-header method-get">GET /api/ict-approval/device-requests</div>
                        <div class="endpoint-body">
                            <strong>ICT Approval Queue</strong><br>
                            Device requests pending ICT officer approval
                        </div>
                    </div>
                </div>
            </div>

            <div id="testing" class="tab-pane">
                <div class="test-section">
                    <h2>🧪 API Testing Interface</h2>
                    <p>Test the API endpoints directly from this interface:</p>
                    
                    <button class="btn" onclick="testConnection()">Test API Connection</button>
                    <button class="btn btn-success" onclick="loadApiSchema()">Load Full API Schema</button>
                    
                    <div id="test-results" style="margin-top: 20px;"></div>
                </div>
            </div>

            <div id="schema" class="tab-pane">
                <h2>📄 OpenAPI 3.0 Schema</h2>
                <p>Complete API specification in OpenAPI 3.0 format:</p>
                <div id="json-schema" class="json-viewer">
                    <div style="text-align: center; padding: 20px; color: #888;">
                        Click "Load Schema" to view the complete API specification
                    </div>
                </div>
                <button class="btn" onclick="loadSchemaDisplay()">Load Schema</button>
                <a href="' . $apiDocsUrl . '" class="btn" target="_blank">View Raw JSON</a>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        function showTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-pane");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            tablinks = document.querySelectorAll(".nav-tabs button");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        // API testing functions
        function testConnection() {
            const resultDiv = document.getElementById("test-results");
            resultDiv.innerHTML = "<div style=\'padding: 10px; background: #fff3cd; border-radius: 4px;\'>Testing API connection...</div>";
            
            fetch("' . $apiDocsUrl . '")
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    resultDiv.innerHTML = `
                        <div style="padding: 15px; background: #d4edda; border-radius: 4px; border: 1px solid #c3e6cb;">
                            <h4 style="margin: 0 0 10px 0; color: #155724;">✅ API Connection Successful</h4>
                            <p><strong>API Title:</strong> ${data.info.title}</p>
                            <p><strong>Version:</strong> ${data.info.version}</p>
                            <p><strong>Endpoints Available:</strong> ${Object.keys(data.paths).length}</p>
                        </div>
                    `;
                    document.getElementById("api-status").textContent = "✅ Online";
                    document.getElementById("api-status").style.color = "#28a745";
                })
                .catch(error => {
                    resultDiv.innerHTML = `
                        <div style="padding: 15px; background: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb;">
                            <h4 style="margin: 0 0 10px 0; color: #721c24;">❌ API Connection Failed</h4>
                            <p><strong>Error:</strong> ${error.message}</p>
                        </div>
                    `;
                    document.getElementById("api-status").textContent = "❌ Offline";
                    document.getElementById("api-status").style.color = "#dc3545";
                });
        }

        function loadApiSchema() {
            const resultDiv = document.getElementById("test-results");
            resultDiv.innerHTML = "<div style=\'padding: 10px; background: #fff3cd; border-radius: 4px;\'>Loading complete API schema...</div>";
            
            fetch("' . $apiDocsUrl . '")
                .then(response => response.json())
                .then(data => {
                    let endpointsList = "";
                    for (const [path, methods] of Object.entries(data.paths)) {
                        for (const [method, details] of Object.entries(methods)) {
                            const methodClass = `method-${method.toLowerCase()}`;
                            endpointsList += `
                                <div style="margin: 5px 0; padding: 8px; border-radius: 4px; background: #f8f9fa;">
                                    <span class="${methodClass}" style="padding: 2px 8px; border-radius: 3px; color: white; font-size: 12px; font-weight: bold;">${method.toUpperCase()}</span>
                                    <code style="margin-left: 10px;">${path}</code>
                                    <div style="font-size: 14px; margin-top: 5px; color: #666;">${details.summary || "No description"}</div>
                                </div>
                            `;
                        }
                    }
                    
                    resultDiv.innerHTML = `
                        <div style="padding: 15px; background: #d4edda; border-radius: 4px; border: 1px solid #c3e6cb;">
                            <h4 style="margin: 0 0 15px 0; color: #155724;">📚 Complete API Schema Loaded</h4>
                            <div style="max-height: 400px; overflow-y: auto;">${endpointsList}</div>
                        </div>
                    `;
                })
                .catch(error => {
                    resultDiv.innerHTML = `
                        <div style="padding: 15px; background: #f8d7da; border-radius: 4px;">
                            <h4 style="margin: 0 0 10px 0; color: #721c24;">❌ Schema Loading Failed</h4>
                            <p>Error: ${error.message}</p>
                        </div>
                    `;
                });
        }

        function loadSchemaDisplay() {
            const schemaDiv = document.getElementById("json-schema");
            schemaDiv.innerHTML = "<div style=\'text-align: center; padding: 20px; color: #888;\'>Loading schema...</div>";
            
            fetch("' . $apiDocsUrl . '")
                .then(response => response.json())
                .then(data => {
                    schemaDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                })
                .catch(error => {
                    schemaDiv.innerHTML = `<div style="color: #e74c3c; text-align: center; padding: 20px;">Failed to load schema: ${error.message}</div>`;
                });
        }

        // Auto-test connection on page load
        window.onload = function() {
            testConnection();
        };
    </script>
</body>
</html>';

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
