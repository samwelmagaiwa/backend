<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SmsModule;
use Illuminate\Support\Facades\Log;

echo "=== ICT Officer SMS Test ===\n\n";

// Test phone number
$phoneNumber = '+255617919104'; // Samwel Magaiwa
$ictOfficerName = 'Samwel Magaiwa';

echo "Testing SMS to: {$ictOfficerName}\n";
echo "Phone: {$phoneNumber}\n\n";

try {
    $smsModule = app(SmsModule::class);
    
    // Check if SMS is enabled
    echo "SMS Enabled: " . ($smsModule->isEnabled() ? 'YES' : 'NO') . "\n";
    echo "SMS Test Mode: " . ($smsModule->isTestMode() ? 'YES' : 'NO') . "\n\n";
    
    // Create test message
    $message = "TEST: New ICT Task Assignment from MNH IT System. You have been assigned to implement access for a staff member. Please login to system. - EABMS";
    
    echo "Sending SMS...\n";
    echo "Message: " . substr($message, 0, 100) . "...\n\n";
    
    $result = $smsModule->sendSms($phoneNumber, $message, 'test_ict_officer');
    
    echo "Result:\n";
    echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    echo "Message: " . $result['message'] . "\n";
    
    if (isset($result['data'])) {
        echo "\nResponse Data:\n";
        print_r($result['data']);
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
