<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserAccess;
use Illuminate\Support\Facades\DB;

echo "=== REQUEST #50 DETAILS ===\n\n";

$request = UserAccess::find(50);

if (!$request) {
    echo "❌ Request not found!\n";
    exit(1);
}

echo "Request Fields:\n";
echo "  ID: {$request->id}\n";
echo "  Staff: {$request->staff_name}\n";
echo "  Request Type (JSON): " . json_encode($request->request_type) . "\n\n";

// Determine request type - handle all variations
$types = [];
$requestTypes = is_array($request->request_type) ? $request->request_type : json_decode($request->request_type ?? '[]', true);

echo "Parsed request types array:\n";
print_r($requestTypes);

echo "\nChecking types:\n";
if (in_array('jeeva_access_request', $requestTypes) || in_array('jeeva_access', $requestTypes) || in_array('jeeva', $requestTypes)) {
    $types[] = 'Jeeva';
    echo "  ✅ Jeeva detected\n";
}
if (in_array('wellsoft_access_request', $requestTypes) || in_array('wellsoft_access', $requestTypes) || in_array('wellsoft', $requestTypes)) {
    $types[] = 'Wellsoft';
    echo "  ✅ Wellsoft detected\n";
}
if (in_array('internet_access_request', $requestTypes) || in_array('internet_access', $requestTypes) || in_array('internet', $requestTypes)) {
    $types[] = 'Internet';
    echo "  ✅ Internet detected\n";
}

$typeStr = implode(' & ', $types) ?: 'Access';

echo "\nFinal message type: {$typeStr}\n";

$ref = 'REQ-' . str_pad($request->id, 6, '0', STR_PAD_LEFT);
$message = "PENDING APPROVAL: {$typeStr} request from {$request->staff_name} requires your review. Ref: {$ref}. Please check the system. - MNH IT";

echo "\nSMS Message:\n";
echo $message . "\n";

echo "\n=== END ===\n";
