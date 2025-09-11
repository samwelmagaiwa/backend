<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\BookingService;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Device Issuing and Approval Flow ===\n\n";

// Check the current state of booking ID 11
echo "1. Checking booking ID 11 (should already be issued):\n";
$booking11 = BookingService::find(11);
if ($booking11) {
    echo "   - Status: {$booking11->status}\n";
    echo "   - ICT Approve: {$booking11->ict_approve}\n";
    echo "   - Device issued at: " . ($booking11->device_issued_at ?? 'null') . "\n";
    echo "   - Has device_condition_issuing: " . (!empty($booking11->device_condition_issuing) ? 'Yes' : 'No') . "\n";
    echo "   - Expected behavior: API call should return 400 (already issued)\n\n";
}

// Check the current state of booking ID 12 (newly created)
echo "2. Checking booking ID 12 (should be fresh/unissued):\n";
$booking12 = BookingService::find(12);
if ($booking12) {
    echo "   - Status: {$booking12->status}\n";
    echo "   - ICT Approve: {$booking12->ict_approve}\n";
    echo "   - Device issued at: " . ($booking12->device_issued_at ?? 'null') . "\n";
    echo "   - Has device_condition_issuing: " . (!empty($booking12->device_condition_issuing) ? 'Yes' : 'No') . "\n";
    echo "   - Expected behavior: API call should succeed and set status to 'in_use'\n\n";
}

echo "3. Workflow explanation:\n";
echo "   Step 1: POST /api/ict-approval/device-requests/{id}/assessment/issuing\n";
echo "           - Issue device and save assessment\n";
echo "           - Borrow from inventory\n";
echo "           - Set status to 'in_use'\n";
echo "           - Set device_issued_at timestamp\n\n";
echo "   Step 2: POST /api/ict-approval/device-requests/{id}/approve\n";
echo "           - Approve the request officially\n";
echo "           - Skip inventory borrowing (already done in step 1)\n";
echo "           - Set ict_approve to 'approved'\n\n";

echo "4. Test URLs:\n";
echo "   - Fresh booking: POST http://localhost:8000/api/ict-approval/device-requests/12/assessment/issuing\n";
echo "   - Already issued: POST http://localhost:8000/api/ict-approval/device-requests/11/assessment/issuing (should return 400)\n\n";

echo "5. Expected API Response for already-issued device:\n";
echo "   {\n";
echo "     \"success\": false,\n";
echo "     \"message\": \"Device has already been issued. Cannot issue the same device twice.\"\n";
echo "   }\n\n";

echo "=== Test Complete ===\n";
