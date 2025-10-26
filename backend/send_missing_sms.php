<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Manually Sending SMS for Request #59 ===\n\n";

// Get the request
$request = \App\Models\UserAccess::find(59);
if (!$request) {
    echo "❌ Request not found\n";
    exit(1);
}

echo "📋 Request Status:\n";
echo "   - ID: {$request->id}\n";
echo "   - Staff: {$request->staff_name}\n";
echo "   - Divisional Status: {$request->divisional_status}\n";
echo "   - Approved At: {$request->divisional_approved_at}\n";
echo "   - SMS Status: {$request->sms_to_ict_director_status}\n\n";

// Get ICT Director
$ictDirector = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'ict_director');
})->first();

if (!$ictDirector) {
    echo "❌ ICT Director not found\n";
    exit(1);
}

echo "👤 ICT Director:\n";
echo "   - Name: {$ictDirector->name}\n";
echo "   - Phone: {$ictDirector->phone}\n\n";

// Get approver (divisional director who approved)
$approver = auth()->user() ?? \App\Models\User::first();

echo "=== Sending SMS Notifications ===\n\n";

try {
    $smsModule = app(\App\Services\SmsModule::class);
    
    // Call the notifyRequestApproved method
    $result = $smsModule->notifyRequestApproved(
        $request,
        $approver,
        'divisional',
        $ictDirector
    );
    
    echo "📧 Notification Results:\n";
    echo "   - Requester notified: " . ($result['requester_notified'] ? '✅ Yes' : '❌ No') . "\n";
    echo "   - Next approver (ICT Director) notified: " . ($result['next_approver_notified'] ? '✅ Yes' : '❌ No') . "\n\n";
    
    // Refresh and check database
    $request->refresh();
    echo "📊 Updated Status:\n";
    echo "   - sms_to_ict_director_status: {$request->sms_to_ict_director_status}\n";
    echo "   - sms_sent_to_ict_director_at: " . ($request->sms_sent_to_ict_director_at ? $request->sms_sent_to_ict_director_at->format('Y-m-d H:i:s') : 'NULL') . "\n\n";
    
    if ($result['next_approver_notified']) {
        echo "✅ SUCCESS! Check phone {$ictDirector->phone} for the SMS message.\n";
    } else {
        echo "❌ SMS failed to send. Check the logs for errors.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Done ===\n";
