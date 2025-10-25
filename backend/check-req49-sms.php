<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserAccess;

echo "=== REQUEST #49 SMS STATUS ===\n\n";

$request = UserAccess::with('department')->find(49);

if (!$request) {
    echo "❌ Request not found!\n";
    exit(1);
}

echo "Request Details:\n";
echo "  ID: {$request->id}\n";
echo "  Staff: {$request->staff_name}\n";
echo "  Department: " . ($request->department->name ?? 'N/A') . "\n";
echo "  Status: {$request->status}\n";

echo "\nSMS Status:\n";
echo "  HOD SMS Status: " . ($request->sms_to_hod_status ?? 'N/A') . "\n";
echo "  HOD SMS Sent At: " . ($request->sms_sent_to_hod_at ?? 'Not sent') . "\n";

echo "\n=== END ===\n";
