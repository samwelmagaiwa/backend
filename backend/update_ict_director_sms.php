<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating ICT Director Phone and SMS Status ===\n\n";

// 1. Update ICT Director phone number
$ictDirector = \App\Models\User::find(2);
if ($ictDirector) {
    $ictDirector->phone = '+255617919104';
    $ictDirector->save();
    echo "✅ Updated ICT Director phone to: {$ictDirector->phone}\n";
} else {
    echo "❌ ICT Director not found\n";
    exit(1);
}

// 2. Get the request and manually send SMS
$request = \App\Models\UserAccess::find(59);
if (!$request) {
    echo "❌ Request #59 not found\n";
    exit(1);
}

echo "✅ Found request #59 (Status: {$request->divisional_status})\n";

// 3. Send SMS notification to ICT Director
try {
    $smsModule = app(\App\Services\SmsModule::class);
    $result = $smsModule->notifyRequestApproved(
        $request,
        \App\Models\User::find($request->divisional_approved_by),
        'divisional',
        $ictDirector
    );
    
    echo "\n📧 SMS Notification Results:\n";
    echo "- Requester notified: " . ($result['requester_notified'] ? '✅ Yes' : '❌ No') . "\n";
    echo "- Next approver (ICT Director) notified: " . ($result['next_approver_notified'] ? '✅ Yes' : '❌ No') . "\n";
    
    // 4. Verify database update
    $request->refresh();
    echo "\n📊 Database Status:\n";
    echo "- sms_to_ict_director_status: {$request->sms_to_ict_director_status}\n";
    echo "- sms_sent_to_ict_director_at: " . ($request->sms_sent_to_ict_director_at ? $request->sms_sent_to_ict_director_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error sending SMS: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Done ===\n";
