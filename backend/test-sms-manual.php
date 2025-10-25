<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SmsModule;

echo "=== MANUAL SMS TEST ===\n\n";

$sms = app(SmsModule::class);

$phone = '+255617919104';
$message = 'TEST: This is a test message from MNH IT System. Request #49 is pending your approval.';

echo "Sending SMS to: {$phone}\n";
echo "Message: {$message}\n\n";

$result = $sms->sendSms($phone, $message, 'test');

echo "\n=== RESULT ===\n";
print_r($result);

echo "\n=== END ===\n";
