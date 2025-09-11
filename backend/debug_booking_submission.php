<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\BookingService;
use App\Models\Department;
use App\Models\DeviceInventory;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug Booking Submission Issues ===\n\n";

// 1. Check if API routes are accessible
echo "1. Checking API Routes:\n";
try {
    $routes = collect(app('router')->getRoutes())->filter(function ($route) {
        return str_contains($route->uri(), 'booking-service');
    });
    
    echo "   - Found " . $routes->count() . " booking-service routes\n";
    foreach ($routes->take(5) as $route) {
        $methods = implode('|', $route->methods());
        echo "   - {$methods} {$route->uri()}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n2. Checking Authentication:\n";
// Check if we have users and tokens
$userCount = User::count();
echo "   - Total users: {$userCount}\n";

$activeTokenCount = \DB::table('personal_access_tokens')->where('expires_at', '>', now())->orWhereNull('expires_at')->count();
echo "   - Active tokens: {$activeTokenCount}\n";

// Get a sample user
$sampleUser = User::first();
if ($sampleUser) {
    echo "   - Sample user: {$sampleUser->name} (ID: {$sampleUser->id})\n";
    $token = $sampleUser->createToken('debug-test');
    echo "   - Created test token for debugging\n";
}

echo "\n3. Checking Database Tables:\n";
try {
    $bookingCount = BookingService::count();
    $departmentCount = Department::count();
    $deviceCount = DeviceInventory::count();
    
    echo "   - BookingService records: {$bookingCount}\n";
    echo "   - Department records: {$departmentCount}\n";
    echo "   - DeviceInventory records: {$deviceCount}\n";
} catch (Exception $e) {
    echo "   ❌ Error checking database: " . $e->getMessage() . "\n";
}

echo "\n4. Checking Recent Booking Attempts:\n";
try {
    $recentBookings = BookingService::orderBy('created_at', 'desc')->limit(5)->get();
    if ($recentBookings->count() > 0) {
        echo "   - Last {$recentBookings->count()} bookings:\n";
        foreach ($recentBookings as $booking) {
            echo "     - ID: {$booking->id}, Status: {$booking->status}, Created: {$booking->created_at}\n";
        }
    } else {
        echo "   - No recent bookings found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking recent bookings: " . $e->getMessage() . "\n";
}

echo "\n5. Checking Laravel Logs for Errors:\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20); // Last 20 lines
    
    $errorLines = array_filter($recentLines, function($line) {
        return str_contains(strtolower($line), 'error') || 
               str_contains(strtolower($line), 'booking') ||
               str_contains(strtolower($line), 'submit');
    });
    
    if (count($errorLines) > 0) {
        echo "   - Recent relevant log entries:\n";
        foreach ($errorLines as $line) {
            echo "     " . substr($line, 0, 100) . "...\n";
        }
    } else {
        echo "   - No recent errors found in logs\n";
    }
} else {
    echo "   - Log file not found\n";
}

echo "\n6. Testing Form Validation:\n";
try {
    // Check what fields are required for booking
    $validator = \Validator::make([], [
        'booking_date' => 'required|date|after_or_equal:today',
        'borrower_name' => 'required|string|max:255',
        'device_type' => 'required|string',
        'department' => 'required',
        'reason' => 'required|string|min:10',
        'return_date' => 'required|date|after:booking_date',
        'return_time' => 'required',
        'phone_number' => 'nullable|string',
        'signature' => 'required'
    ]);
    
    echo "   - Validation rules check: ";
    if ($validator->fails()) {
        echo "Missing required fields detected (this is expected)\n";
        $errors = $validator->errors();
        echo "     Required fields: " . implode(', ', array_keys($errors->toArray())) . "\n";
    } else {
        echo "No validation errors (unexpected)\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error testing validation: " . $e->getMessage() . "\n";
}

echo "\n7. Common Issues to Check:\n";
echo "   - Frontend Issues:\n";
echo "     • JavaScript errors in browser console?\n";
echo "     • Network tab shows failed requests?\n";
echo "     • Form validation preventing submission?\n";
echo "     • Button click event not attached?\n";
echo "   \n";
echo "   - Backend Issues:\n";
echo "     • CORS headers configured?\n";
echo "     • Authentication middleware working?\n";
echo "     • Validation failing silently?\n";
echo "     • Database connection issues?\n";
echo "   \n";
echo "   - To test manually:\n";
echo "     • Check browser Developer Tools > Network tab\n";
echo "     • Look for 422 (validation) or 500 (server) errors\n";
echo "     • Check if POST request is being made to /api/booking-service/bookings\n";
echo "     • Verify Authorization: Bearer token is present\n";

echo "\n=== Debug Complete ===\n";
echo "Next steps:\n";
echo "1. Check browser console for JavaScript errors\n";
echo "2. Check Network tab for failed API requests\n";
echo "3. Try submitting form and watch for API calls\n";
echo "4. Check if form validation is blocking submission\n";
