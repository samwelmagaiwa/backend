<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserAccess;

echo "=== Fixing SMS Status for Request 52 ===\n\n";

$request = UserAccess::find(52);

if (!$request) {
    echo "Request 52 not found!\n";
    exit(1);
}

echo "Current Status:\n";
echo "  Staff: {$request->staff_name}\n";
echo "  SMS Status: " . ($request->sms_to_hod_status ?? 'NULL') . "\n";
echo "  SMS Sent At: " . ($request->sms_sent_to_hod_at ?? 'NULL') . "\n\n";

echo "Since you confirmed SMS was received, updating status to 'sent'...\n";

$request->sms_to_hod_status = 'sent';
$request->sms_sent_to_hod_at = now();
$request->save();

echo "Updated!\n\n";

echo "New Status:\n";
$request->refresh();
echo "  SMS Status: {$request->sms_to_hod_status}\n";
echo "  SMS Sent At: {$request->sms_sent_to_hod_at}\n";

echo "\n=== Done ===\n";
