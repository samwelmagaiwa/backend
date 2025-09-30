<?php
/**
 * API Endpoint Extraction and Analysis Script
 * This script analyzes the routes/api.php file to extract all API endpoints
 */

$routesFile = __DIR__ . '/routes/api.php';

if (!file_exists($routesFile)) {
    die("Routes file not found: $routesFile\n");
}

$content = file_get_contents($routesFile);

// Initialize counters
$endpoints = [];
$methodCounts = [
    'GET' => 0,
    'POST' => 0,
    'PUT' => 0,
    'PATCH' => 0,
    'DELETE' => 0,
    'OPTIONS' => 0,
    'HEAD' => 0
];

$categories = [
    'Health' => [],
    'Auth' => [],
    'Admin' => [],
    'User Access' => [],
    'Booking Service' => [],
    'ICT Approval' => [],
    'Both Service Form' => [],
    'User Roles' => [],
    'Department HOD' => [],
    'Device Inventory' => [],
    'User Dashboard' => [],
    'HOD Combined Access' => [],
    'Divisional Combined Access' => [],
    'ICT Director Combined Access' => [],
    'Head of IT' => [],
    'ICT Officer' => [],
    'User Access Workflow' => [],
    'Profile' => [],
    'Notifications' => [],
    'Module Access' => [],
    'Module Requests' => [],
    'Access Rights' => [],
    'Implementation Workflow' => [],
    'Security Test' => [],
    'Request Status' => [],
    'Onboarding' => [],
    'Declaration' => []
];

// Extract individual route definitions
preg_match_all('/Route::(get|post|put|patch|delete|options|head)\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    $method = strtoupper($match[1]);
    $path = $match[2];
    
    $methodCounts[$method]++;
    $endpoints[] = ['method' => $method, 'path' => $path];
    
    // Categorize endpoint
    $category = categorizeEndpoint($path);
    $categories[$category][] = ['method' => $method, 'path' => $path];
}

// Extract apiResource routes
preg_match_all('/Route::apiResource\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $resourceMatches, PREG_SET_ORDER);

foreach ($resourceMatches as $match) {
    $resourcePath = $match[1];
    
    // apiResource creates: index (GET), store (POST), show (GET), update (PUT), destroy (DELETE)
    $resourceEndpoints = [
        ['method' => 'GET', 'path' => $resourcePath],
        ['method' => 'POST', 'path' => $resourcePath],
        ['method' => 'GET', 'path' => $resourcePath . '/{id}'],
        ['method' => 'PUT', 'path' => $resourcePath . '/{id}'],
        ['method' => 'DELETE', 'path' => $resourcePath . '/{id}']
    ];
    
    foreach ($resourceEndpoints as $endpoint) {
        $methodCounts[$endpoint['method']]++;
        $endpoints[] = $endpoint;
        
        $category = categorizeEndpoint($endpoint['path']);
        $categories[$category][] = $endpoint;
    }
}

function categorizeEndpoint($path) {
    if (str_contains($path, 'health')) return 'Health';
    if (str_contains($path, 'login') || str_contains($path, 'logout') || str_contains($path, 'register') || str_contains($path, 'sessions') || str_contains($path, 'current-user') || str_contains($path, 'role-redirect')) return 'Auth';
    if (str_contains($path, 'admin/users') || str_contains($path, 'admin/departments') || str_contains($path, 'admin/onboarding')) return 'Admin';
    if (str_contains($path, 'user-access') && !str_contains($path, 'workflow')) return 'User Access';
    if (str_contains($path, 'booking-service')) return 'Booking Service';
    if (str_contains($path, 'ict-approval')) return 'ICT Approval';
    if (str_contains($path, 'both-service-form')) return 'Both Service Form';
    if (str_contains($path, 'user-roles')) return 'User Roles';
    if (str_contains($path, 'department-hod')) return 'Department HOD';
    if (str_contains($path, 'device-inventory')) return 'Device Inventory';
    if (str_contains($path, 'user/dashboard') || str_contains($path, 'user/request-status') || str_contains($path, 'user/recent-activity')) return 'User Dashboard';
    if (str_contains($path, 'hod/') && str_contains($path, 'combined-access')) return 'HOD Combined Access';
    if (str_contains($path, 'divisional/')) return 'Divisional Combined Access';
    if (str_contains($path, 'dict/')) return 'ICT Director Combined Access';
    if (str_contains($path, 'head-of-it')) return 'Head of IT';
    if (str_contains($path, 'ict-officer')) return 'ICT Officer';
    if (str_contains($path, 'user-access-workflow')) return 'User Access Workflow';
    if (str_contains($path, 'profile')) return 'Profile';
    if (str_contains($path, 'notifications')) return 'Notifications';
    if (str_contains($path, 'module-access-approval')) return 'Module Access';
    if (str_contains($path, 'module-requests')) return 'Module Requests';
    if (str_contains($path, 'access-rights-approval')) return 'Access Rights';
    if (str_contains($path, 'implementation-workflow')) return 'Implementation Workflow';
    if (str_contains($path, 'security-test')) return 'Security Test';
    if (str_contains($path, 'request-status')) return 'Request Status';
    if (str_contains($path, 'onboarding')) return 'Onboarding';
    if (str_contains($path, 'declaration')) return 'Declaration';
    if (str_contains($path, '/user')) return 'User Dashboard';
    
    return 'Miscellaneous';
}

// Output results
echo "=== API ENDPOINT ANALYSIS RESULTS ===\n\n";

echo "TOTAL ENDPOINTS: " . count($endpoints) . "\n\n";

echo "HTTP METHOD DISTRIBUTION:\n";
foreach ($methodCounts as $method => $count) {
    if ($count > 0) {
        echo "- $method: $count endpoints\n";
    }
}

echo "\nENDPOINTS BY CATEGORY:\n";
foreach ($categories as $category => $categoryEndpoints) {
    if (!empty($categoryEndpoints)) {
        echo "\n$category (" . count($categoryEndpoints) . " endpoints):\n";
        foreach ($categoryEndpoints as $endpoint) {
            echo "  {$endpoint['method']} {$endpoint['path']}\n";
        }
    }
}

echo "\n=== COMPLETE ENDPOINT LIST ===\n";
foreach ($endpoints as $endpoint) {
    echo "{$endpoint['method']} {$endpoint['path']}\n";
}

// Generate JSON output for documentation
$apiData = [
    'total_endpoints' => count($endpoints),
    'method_counts' => array_filter($methodCounts),
    'categories' => array_filter($categories, function($cat) { return !empty($cat); }),
    'all_endpoints' => $endpoints
];

file_put_contents(__DIR__ . '/api_analysis.json', json_encode($apiData, JSON_PRETTY_PRINT));
echo "\nDetailed analysis saved to api_analysis.json\n";
?>
