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
                ['name' => 'Module Requests', 'description' => 'Wellsoft and Jeeva module access requests'],
                ['name' => 'Device Booking', 'description' => 'ICT device booking and management'],
                ['name' => 'Admin', 'description' => 'Administrative functions'],
                ['name' => 'Workflow', 'description' => 'Approval workflow management'],
                ['name' => 'ICT Approval', 'description' => 'ICT officer approval processes'],
                ['name' => 'Profile', 'description' => 'User profile management'],
                ['name' => 'Dashboard', 'description' => 'Dashboard and statistics endpoints']
            ],
            'paths' => $this->generatePaths()
        ];

        return response()->json($apiDoc, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Generate basic paths from the documented controllers
     */
    private function generatePaths()
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
            '/logout' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'User Logout',
                    'description' => 'Logout user from current session',
                    'operationId' => 'logout',
                    'security' => [['sanctum' => []]],
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
                        ],
                        [
                            'name' => 'per_page',
                            'in' => 'query',
                            'description' => 'Items per page',
                            'schema' => [
                                'type' => 'integer',
                                'default' => 15,
                                'maximum' => 100
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Success',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean'],
                                            'data' => ['type' => 'object'],
                                            'message' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ]
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
                        '201' => [
                            'description' => 'User access request created successfully'
                        ]
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
                        ],
                        [
                            'name' => 'device_type',
                            'in' => 'query',
                            'description' => 'Filter by device type',
                            'schema' => ['type' => 'string']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Device borrowing requests retrieved successfully'
                        ],
                        '403' => [
                            'description' => 'Unauthorized - ICT officer access required'
                        ]
                    ]
                ]
            ],
            '/ict-approval/device-requests/{requestId}' => [
                'get' => [
                    'tags' => ['ICT Approval'],
                    'summary' => 'Get Device Borrowing Request Details',
                    'description' => 'Retrieve detailed information for a specific device borrowing request',
                    'operationId' => 'getDeviceBorrowingRequestDetails',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'requestId',
                            'in' => 'path',
                            'description' => 'Device borrowing request ID',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Request details retrieved successfully'
                        ],
                        '404' => [
                            'description' => 'Request not found'
                        ],
                        '403' => [
                            'description' => 'Unauthorized - ICT officer access required'
                        ]
                    ]
                ]
            ],
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
                        '200' => [
                            'description' => 'Device bookings retrieved successfully'
                        ]
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
                                        'purpose' => ['type' => 'string', 'example' => 'Official work'],
                                        'borrower_name' => ['type' => 'string', 'example' => 'John Doe'],
                                        'department' => ['type' => 'string', 'example' => 'IT Department']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Device booking created successfully'
                        ]
                    ]
                ]
            ],
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
