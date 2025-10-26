<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing SMS Configuration ===\n\n";

// 1. Check SMS configuration
echo "📋 SMS Configuration:\n";
echo "   - SMS Enabled: " . (config('sms.enabled') ? '✅ Yes' : '❌ No') . "\n";
echo "   - Test Mode: " . (config('sms.test_mode') ? '⚠️ Yes (test endpoint)' : '✅ No (production)') . "\n";
echo "   - API URL: " . config('sms.api_url') . "\n";
echo "   - API Key: " . (config('sms.api_key') ? '✅ Configured' : '❌ Not configured') . "\n";
echo "   - Sender ID: " . config('sms.sender_id') . "\n\n";

// 2. Get ICT Director details
$ictDirector = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'ict_director');
})->first();

if (!$ictDirector) {
    echo "❌ ICT Director not found\n";
    exit(1);
}

echo "👤 ICT Director Details:\n";
echo "   - Name: {$ictDirector->name}\n";
echo "   - Email: {$ictDirector->email}\n";
echo "   - Phone: {$ictDirector->phone}\n\n";

// 3. Test SMS sending
echo "=== Sending Test SMS ===\n\n";

try {
    $smsModule = app(\App\Services\SmsModule::class);
    
    // Check if SMS service is enabled
    if (!$smsModule->isEnabled()) {
        echo "❌ SMS service is DISABLED in configuration\n";
        echo "   Please set SMS_ENABLED=true in .env file\n";
        exit(1);
    }
    
    echo "✅ SMS service is enabled\n";
    echo "⚠️  Test Mode: " . ($smsModule->isTestMode() ? 'YES (using test endpoint)' : 'NO (using production endpoint)') . "\n\n";
    
    // Send test SMS
    $testMessage = "TEST SMS from MNH IT System - If you receive this message, SMS notifications are working correctly. Time: " . now()->format('H:i:s');
    
    echo "📱 Sending test SMS to: {$ictDirector->phone}\n";
    echo "📝 Message: {$testMessage}\n\n";
    
    $result = $smsModule->sendSms($ictDirector->phone, $testMessage, 'test');
    
    echo "=== Result ===\n";
    if ($result['success']) {
        echo "✅ SMS SENT SUCCESSFULLY!\n";
        echo "   Message: {$result['message']}\n";
        echo "   \n";
        echo "   Please check phone {$ictDirector->phone} for the test message.\n";
    } else {
        echo "❌ SMS FAILED TO SEND\n";
        echo "   Error: {$result['message']}\n";
        echo "   \n";
        echo "   ⚠️  When you approve the request, the SMS will NOT be sent!\n";
        echo "   Please fix the SMS configuration before approving.\n";
    }
    
    // 4. Check SMS logs
    echo "\n=== Recent SMS Logs ===\n";
    $recentLogs = \App\Models\SmsLog::orderBy('created_at', 'desc')
        ->take(5)
        ->get(['phone_number', 'status', 'type', 'created_at']);
    
    if ($recentLogs->count() > 0) {
        foreach ($recentLogs as $log) {
            $statusIcon = $log->status === 'sent' ? '✅' : '❌';
            echo "   {$statusIcon} {$log->phone_number} - {$log->status} ({$log->type}) - {$log->created_at->format('Y-m-d H:i:s')}\n";
        }
    } else {
        echo "   No SMS logs found\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Summary ===\n";
echo "If the test SMS was sent successfully, you can safely approve the request.\n";
echo "The ICT Director will receive an SMS notification when you approve.\n\n";
