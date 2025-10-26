<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating SMS Status for Request 59 ===\n\n";

// Update the SMS status to 'sent' since the SMS was already received
$affected = DB::table('user_access')
    ->where('id', 59)
    ->update([
        'sms_to_ict_officer_status' => 'sent',
        'sms_sent_to_ict_officer_at' => now()
    ]);

echo "Rows updated: {$affected}\n";

// Verify the update
$request = DB::table('user_access')
    ->where('id', 59)
    ->select('id', 'staff_name', 'sms_to_ict_officer_status', 'sms_sent_to_ict_officer_at')
    ->first();

if ($request) {
    echo "\nUpdated values:\n";
    echo "Request ID: {$request->id}\n";
    echo "Staff Name: {$request->staff_name}\n";
    echo "SMS Status: {$request->sms_to_ict_officer_status}\n";
    echo "SMS Sent At: {$request->sms_sent_to_ict_officer_at}\n";
}

echo "\n=== Done ===\n";
