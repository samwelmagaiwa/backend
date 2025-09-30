<?php

/**
 * Controller Analysis Script
 * Extracts all public methods from API controllers
 */

$controllerDir = '/c/xampp/htdocs/lara-API-vue/backend/app/Http/Controllers';
$controllers = [];

function scanDirectory($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

function extractMethods($file) {
    $content = file_get_contents($file);
    $methods = [];
    
    // Extract class name
    preg_match('/class\s+(\w+)Controller/', $content, $classMatch);
    $className = $classMatch[1] ?? 'Unknown';
    
    // Extract public methods
    preg_match_all('/public\s+function\s+(\w+)\s*\(([^)]*)\)/', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $methodName = $match[1];
        $parameters = $match[2] ?? '';
        
        // Skip magic methods and constructors
        if (strpos($methodName, '__') === 0) continue;
        
        $methods[] = [
            'name' => $methodName,
            'parameters' => trim($parameters),
        ];
    }
    
    return [
        'class' => $className,
        'file' => $file,
        'methods' => $methods
    ];
}

// Scan all controller files
$controllerFiles = scanDirectory($controllerDir);
$results = [];

foreach ($controllerFiles as $file) {
    $relativePath = str_replace('/c/xampp/htdocs/lara-API-vue/backend/app/Http/Controllers/', '', $file);
    $analysis = extractMethods($file);
    $analysis['path'] = $relativePath;
    $results[] = $analysis;
}

// Sort by path
usort($results, function($a, $b) {
    return strcmp($a['path'], $b['path']);
});

// Output results
echo "=== API CONTROLLER ANALYSIS ===\n\n";
echo "Total Controllers Found: " . count($results) . "\n\n";

foreach ($results as $controller) {
    echo "Controller: {$controller['class']}\n";
    echo "Path: {$controller['path']}\n";
    echo "Methods: " . count($controller['methods']) . "\n";
    
    if (!empty($controller['methods'])) {
        foreach ($controller['methods'] as $method) {
            echo "  - {$method['name']}({$method['parameters']})\n";
        }
    }
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// Generate summary statistics
$totalMethods = 0;
$controllersByType = [];

foreach ($results as $controller) {
    $totalMethods += count($controller['methods']);
    
    // Categorize by path
    if (strpos($controller['path'], 'Api/v1/') !== false) {
        $controllersByType['API v1'][] = $controller;
    } elseif (strpos($controller['path'], 'Api/') !== false) {
        $controllersByType['API'][] = $controller;
    } else {
        $controllersByType['Other'][] = $controller;
    }
}

echo "=== SUMMARY STATISTICS ===\n";
echo "Total Methods: {$totalMethods}\n";
foreach ($controllersByType as $type => $typeControllers) {
    $typeMethods = array_sum(array_map(function($c) { return count($c['methods']); }, $typeControllers));
    echo "{$type} Controllers: " . count($typeControllers) . " (Methods: {$typeMethods})\n";
}

?>
