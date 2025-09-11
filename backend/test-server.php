<?php
// Simple test script to verify Laravel is working
echo "Laravel Server Test\n";
echo "===================\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "Laravel App Path: " . (file_exists(__DIR__ . '/app') ? 'Found' : 'Not Found') . "\n";
echo "Vendor Path: " . (file_exists(__DIR__ . '/vendor') ? 'Found' : 'Not Found') . "\n";
echo "Routes File: " . (file_exists(__DIR__ . '/routes/api.php') ? 'Found' : 'Not Found') . "\n";
echo "\nIf you see this message, PHP is working!\n";
echo "Now run: php artisan serve --host=127.0.0.1 --port=8000\n";
?>