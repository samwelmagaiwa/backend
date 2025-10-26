<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$request = \App\Models\UserAccess::find(59);

echo "Request #59 SMS Status:\n";
echo "- SMS to ICT Director Status: {$request->sms_to_ict_director_status}\n";
echo "- SMS Sent At: {$request->sms_sent_to_ict_director_at}\n";
