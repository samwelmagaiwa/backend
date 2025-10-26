<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$request = \App\Models\UserAccess::find(59);

echo "Request #59 Approval Timeline:\n\n";
echo "Divisional Approval:\n";
echo "  - Status: {$request->divisional_status}\n";
echo "  - Approved By: {$request->divisional_approved_by_name}\n";
echo "  - Approved At: {$request->divisional_approved_at}\n";
echo "  - SMS to ICT Director: {$request->sms_to_ict_director_status}\n";
echo "  - SMS Sent At: {$request->sms_sent_to_ict_director_at}\n\n";

// Check when the request was last modified
echo "Last Modified: {$request->updated_at}\n";
