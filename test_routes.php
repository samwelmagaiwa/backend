<?php

// Simple script to test if routes are working
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Get all registered routes
$routes = $app->make('router')->getRoutes();

echo "Registered API Routes:\n";
echo "=====================\n";

foreach ($routes as $route) {
    $uri = $route->uri();
    $methods = implode('|', $route->methods());
    
    // Only show API routes
    if (strpos($uri, 'api/') === 0) {
        echo sprintf("%-8s %s\n", $methods, $uri);
    }
}

echo "\nLooking for login route specifically:\n";
echo "====================================\n";

$loginRoutes = array_filter($routes->getRoutes(), function($route) {
    return strpos($route->uri(), 'login') !== false;
});

if (empty($loginRoutes)) {
    echo "❌ No login routes found!\n";
} else {
    foreach ($loginRoutes as $route) {
        $uri = $route->uri();
        $methods = implode('|', $route->methods());
        echo sprintf("✅ %-8s %s\n", $methods, $uri);
    }
}