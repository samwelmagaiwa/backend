<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SMS System Status Check ===\n\n";

// 1. Check SMS Configuration
echo "1. SMS Configuration:\n";
echo "   - SMS_ENABLED: " . (config('sms.enabled') ? 'YES' : 'NO') . "\n";
echo "   - SMS_TEST_MODE: " . (config('sms.test_mode') ? 'YES' : 'NO') . "\n";
echo "   - API_KEY: " . (config('sms.api_key') ? 'SET' : 'NOT SET') . "\n\n";

// 2. Check Recent Requests
echo "2. Recent User Access Requests:\n";
$requests = \App\Models\UserAccess::orderBy('id', 'desc')->take(5)->get(['id', 'staff_name', 'hod_status', 'department_id']);
if ($requests->isEmpty()) {
    echo "   No requests found\n";
} else {
    foreach ($requests as $req) {
        echo "   - ID: {$req->id}, Staff: {$req->staff_name}, Status: {$req->hod_status}, Dept: {$req->department_id}\n";
    }
}
echo "\n";

// 3. Check Users with Phone Numbers
echo "3. Users with Phone Numbers:\n";
$usersWithPhone = \App\Models\User::whereNotNull('phone')->where('phone', '!=', '')->count();
$totalUsers = \App\Models\User::count();
echo "   - {$usersWithPhone} out of {$totalUsers} users have phone numbers\n\n";

// 4. Check HODs
echo "4. HODs in System:\n";
$hods = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'head_of_department');
})->with('departmentsAsHOD')->get();

if ($hods->isEmpty()) {
    echo "   No HODs found\n";
} else {
    foreach ($hods as $hod) {
        $depts = $hod->departmentsAsHOD->pluck('name')->implode(', ');
        $phone = $hod->phone ?: 'NO PHONE';
        echo "   - {$hod->name} ({$phone}) - Depts: {$depts}\n";
    }
}
echo "\n";

// 5. Check Pending Requests
echo "5. Pending HOD Approval Requests:\n";
$pending = \App\Models\UserAccess::whereNull('hod_status')
    ->orWhere('hod_status', 'pending')
    ->get(['id', 'staff_name', 'department_id']);
    
if ($pending->isEmpty()) {
    echo "   No pending requests\n";
} else {
    foreach ($pending as $req) {
        echo "   - REQ-" . str_pad($req->id, 6, '0', STR_PAD_LEFT) . ": {$req->staff_name} (Dept: {$req->department_id})\n";
    }
}
echo "\n";

// 6. Test SMS Module
echo "6. SMS Module Test:\n";
try {
    $sms = app(\App\Services\SmsModule::class);
    echo "   - Module loaded: YES\n";
    echo "   - Is enabled: " . ($sms->isEnabled() ? 'YES' : 'NO') . "\n";
    echo "   - Is test mode: " . ($sms->isTestMode() ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "   - ERROR: {$e->getMessage()}\n";
}

echo "\n=== End of Status Check ===\n";
