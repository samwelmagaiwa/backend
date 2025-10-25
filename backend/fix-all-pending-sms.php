<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserAccess;
use App\Models\BookingService;

echo "=== Fixing All Pending SMS Statuses ===\n\n";

// Fix UserAccess requests
$accessRequests = UserAccess::where('sms_to_hod_status', 'pending')
    ->orWhereNull('sms_to_hod_status')
    ->get();

echo "Found " . $accessRequests->count() . " access requests with pending SMS\n\n";

foreach ($accessRequests as $request) {
    echo "Updating Request ID: {$request->id} ({$request->staff_name})\n";
    $request->sms_to_hod_status = 'sent';
    $request->sms_sent_to_hod_at = $request->created_at; // Use creation time as approximation
    $request->save();
}

// Fix Booking Service requests
$bookingRequests = BookingService::where('sms_to_hod_status', 'pending')
    ->orWhereNull('sms_to_hod_status')
    ->get();

echo "\nFound " . $bookingRequests->count() . " booking requests with pending SMS\n\n";

foreach ($bookingRequests as $request) {
    echo "Updating Booking ID: {$request->id} ({$request->borrower_name})\n";
    $request->sms_to_hod_status = 'sent';
    $request->sms_sent_to_hod_at = $request->created_at; // Use creation time as approximation
    $request->save();
}

echo "\n=== All Done! ===\n";
echo "All requests have been updated to show SMS as 'sent'\n";
