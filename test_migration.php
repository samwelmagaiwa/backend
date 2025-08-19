<?php

// Simple script to test migration dependencies
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Get all migration files
$migrationPath = __DIR__ . '/database/migrations';
$files = glob($migrationPath . '/*.php');

echo "Migration files in order:\n";
echo "========================\n";

sort($files);
foreach ($files as $file) {
    $filename = basename($file);
    echo $filename . "\n";
}

echo "\nChecking for potential issues:\n";
echo "==============================\n";

// Check if users table migration exists
$usersExists = false;
$onboardingExists = false;

foreach ($files as $file) {
    $filename = basename($file);
    if (strpos($filename, 'create_users_table') !== false) {
        $usersExists = true;
        echo "✅ Users table migration found: $filename\n";
    }
    if (strpos($filename, 'create_user_onboarding_table') !== false) {
        $onboardingExists = true;
        echo "✅ User onboarding migration found: $filename\n";
    }
}

if (!$usersExists) {
    echo "❌ Users table migration not found!\n";
}

if (!$onboardingExists) {
    echo "❌ User onboarding migration not found!\n";
}

if ($usersExists && $onboardingExists) {
    echo "✅ All required migrations found. Should be safe to run.\n";
}