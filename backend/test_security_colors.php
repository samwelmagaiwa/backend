<?php

/**
 * Security Color Test Demo
 * 
 * This demonstrates what the colorized security output looks like.
 * For actual health checks, use: php artisan config:clear
 * Or call the API endpoint: /api/security-test/health
 */

// ANSI color codes
$GREEN = "\e[32m"; // green
$RED   = "\e[31m"; // red
$RESET = "\e[0m";  // reset

echo "\n🔒 Security Feature Health Check Demo\n";
echo "======================================\n\n";

// Mock security checks to show colors
$demoChecks = [
    'CORS Protection' => ['ok' => true, 'message' => 'CORS configured for paths: api/*, sanctum/csrf-cookie'],
    'Security Headers (Helmet-like)' => ['ok' => true, 'message' => 'SecurityHeaders middleware available'],
    'Input Sanitization' => ['ok' => true, 'message' => 'InputSanitization middleware available'],
    'XSS Protection' => ['ok' => true, 'message' => 'XSSProtection middleware available'],
    'Rate Limiting (60 req/min)' => ['ok' => true, 'message' => 'RouteServiceProvider available (rate limiters configured)'],
    'SQL Injection Prevention' => ['ok' => true, 'message' => 'Eloquent ORM available (use parameter binding)'],
    'CSRF Protection' => ['ok' => true, 'message' => 'VerifyCsrfToken middleware available (enabled for web)'],
    'Demo Failure Example' => ['ok' => false, 'message' => 'This shows how failures appear in red'],
];

$allOk = true;
foreach ($demoChecks as $check) {
    if (!$check['ok']) {
        $allOk = false;
        break;
    }
}

// Overall status
echo "[SECURITY] Boot checks: " . ($allOk ? $GREEN . 'OK' . $RESET : $RED . 'FAIL' . $RESET) . "\n";

// Individual checks
foreach ($demoChecks as $name => $result) {
    $color = $result['ok'] ? $GREEN : $RED;
    $status = $result['ok'] ? 'OK' : 'FAIL';
    echo "[SECURITY] {$name}: {$status} - {$color}{$result['message']}{$RESET}\n";
}

echo "\n";
echo "This is what the colorized output looks like:\n";
echo "- " . $GREEN . "Green" . $RESET . " indicates everything is working correctly\n";
echo "- " . $RED . "Red" . $RESET . " indicates security issues that need attention\n\n";

echo "To see actual security health checks:\n";
echo "• Run: php artisan config:clear\n";
echo "• Or call: curl http://localhost:8000/api/security-test/health\n\n";
