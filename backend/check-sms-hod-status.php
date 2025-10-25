<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking SMS to HOD Status ===\n\n";

// Check user_access table
echo "User Access Requests:\n";
$accessRequests = DB::table('user_access')
    ->select('id', 'staff_name', 'sms_to_hod_status', 'sms_sent_to_hod_at', 'created_at')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($accessRequests as $req) {
    $smsStatus = $req->sms_to_hod_status ?? 'NULL';
    $smsSentAt = $req->sms_sent_to_hod_at ?? 'NULL';
    echo "  ID: {$req->id} | {$req->staff_name} | SMS: {$smsStatus} | Sent: {$smsSentAt}\n";
}

echo "\nBooking Requests:\n";
$bookingRequests = DB::table('booking_service')
    ->select('id', 'borrower_name', 'sms_to_hod_status', 'sms_sent_to_hod_at', 'created_at')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($bookingRequests as $req) {
    $smsStatus = $req->sms_to_hod_status ?? 'NULL';
    $smsSentAt = $req->sms_sent_to_hod_at ?? 'NULL';
    echo "  ID: {$req->id} | {$req->borrower_name} | SMS: {$smsStatus} | Sent: {$smsSentAt}\n";
}

echo "\n=== Done ===\n";
