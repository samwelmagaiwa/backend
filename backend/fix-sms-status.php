<?php

/**
 * Fix SMS Status Mismatch
 * 
 * Updates user_access records where SMS was sent successfully 
 * but the status wasn't updated in the database.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserAccess;
use App\Models\SmsLog;
use Illuminate\Support\Facades\DB;

echo "=== Fixing SMS Status Mismatch ===\n\n";

// Find all user_access records with pending SMS status
$pendingRequests = UserAccess::where('sms_to_hod_status', 'pending')
    ->orWhereNull('sms_to_hod_status')
    ->orderBy('id', 'desc')
    ->get();

echo "Found {$pendingRequests->count()} requests with pending/null SMS status\n\n";

$fixed = 0;
$skipped = 0;

foreach ($pendingRequests as $request) {
    // Check if there's a successful SMS log for this request
    // We need to find SMS sent around the time the request was created
    $smsLog = SmsLog::where('type', 'pending_notification')
        ->where('status', 'sent')
        ->where('created_at', '>=', $request->created_at)
        ->where('created_at', '<=', $request->created_at->addMinutes(5))
        ->orderBy('created_at', 'asc')
        ->first();
    
    if ($smsLog) {
        // Update the request status
        $request->update([
            'sms_to_hod_status' => 'sent',
            'sms_sent_to_hod_at' => $smsLog->sent_at ?? $smsLog->created_at
        ]);
        
        echo "✓ Fixed Request ID {$request->id} ({$request->staff_name})\n";
        echo "  SMS sent at: {$smsLog->sent_at}\n";
        echo "  Updated status to: sent\n\n";
        $fixed++;
    } else {
        // Check if HOD has no phone (SMS couldn't be sent)
        $hod = \App\Models\User::whereHas('departmentsAsHOD', function($q) use ($request) {
            $q->where('departments.id', $request->department_id);
        })->first();
        
        if (!$hod || !$hod->phone) {
            $request->update([
                'sms_to_hod_status' => 'failed',
                'sms_sent_to_hod_at' => null
            ]);
            echo "✗ Request ID {$request->id} - HOD has no phone number (marked as failed)\n\n";
            $fixed++;
        } else {
            echo "⊘ Skipped Request ID {$request->id} - No matching SMS log found\n\n";
            $skipped++;
        }
    }
}

echo "\n=== Summary ===\n";
echo "Fixed: {$fixed}\n";
echo "Skipped: {$skipped}\n";
echo "Total: {$pendingRequests->count()}\n";
