<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SMS DIAGNOSTIC TOOL ===\n\n";

// 1. Check SMS enabled
$smsEnabled = config('sms.enabled');
echo "1. SMS Enabled: " . ($smsEnabled ? "✅ YES" : "❌ NO - THIS IS THE PROBLEM!") . "\n";
echo "   From .env: " . env('SMS_ENABLED', 'not set') . "\n\n";

// 2. Check Divisional Director exists
$divDirector = \App\Models\User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

echo "2. Divisional Director Found: " . ($divDirector ? "✅ YES" : "❌ NO - THIS IS THE PROBLEM!") . "\n";

if ($divDirector) {
    echo "   Name: " . $divDirector->name . "\n";
    echo "   Email: " . $divDirector->email . "\n";
    echo "   Phone: " . ($divDirector->phone ? "✅ " . $divDirector->phone : "❌ NO PHONE - THIS IS THE PROBLEM!") . "\n";
} else {
    // Try to find what divisional roles exist
    $roles = \Spatie\Permission\Models\Role::where('name', 'like', '%division%')->get();
    echo "   Available divisional roles: " . $roles->pluck('name')->implode(', ') . "\n";
}

echo "\n";

// 3. Check recent HOD approvals
$recentApprovals = \App\Models\UserAccess::where('hod_status', 'hod_approved')
    ->orderBy('hod_approved_at', 'desc')
    ->limit(3)
    ->get(['id', 'staff_name', 'sms_to_divisional_status', 'sms_sent_to_divisional_at', 'hod_approved_at']);

echo "3. Recent HOD Approvals:\n";
if ($recentApprovals->isEmpty()) {
    echo "   ❌ No HOD-approved requests found\n";
} else {
    foreach ($recentApprovals as $req) {
        echo "   Request #{$req->id} - {$req->staff_name}\n";
        echo "   Approved At: " . ($req->hod_approved_at ? $req->hod_approved_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
        echo "   SMS Status: " . ($req->sms_to_divisional_status ?? 'NULL - PROBLEM!') . "\n";
        echo "   SMS Sent At: " . ($req->sms_sent_to_divisional_at ?? 'Never - PROBLEM!') . "\n\n";
    }
}

// 4. Check if SMS Module works
try {
    $sms = app(\App\Services\SmsModule::class);
    echo "4. SMS Module Status:\n";
    echo "   Module Loaded: ✅ YES\n";
    echo "   Enabled: " . ($sms->isEnabled() ? "✅ YES" : "❌ NO - THIS IS THE PROBLEM!") . "\n";
    echo "   Test Mode: " . ($sms->isTestMode() ? "⚠️ YES (using test endpoint)" : "✅ NO (using live endpoint)") . "\n";
} catch (\Exception $e) {
    echo "4. SMS Module Status:\n";
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSIS ===\n";

// Provide diagnosis
$problems = [];

if (!$smsEnabled) {
    $problems[] = "❌ SMS is DISABLED in .env file. Set SMS_ENABLED=true";
}

if (!$divDirector) {
    $problems[] = "❌ No Divisional Director found with role 'divisional_director'";
} elseif (!$divDirector->phone) {
    $problems[] = "❌ Divisional Director has no phone number";
}

if (!$recentApprovals->isEmpty()) {
    $needsSms = $recentApprovals->where('sms_to_divisional_status', null)->count();
    if ($needsSms > 0) {
        $problems[] = "⚠️ {$needsSms} HOD-approved request(s) have NULL SMS status - SMS was never sent";
    }
}

if (empty($problems)) {
    echo "✅ No obvious problems found. Check Laravel logs for SMS errors.\n";
    echo "   Log file: storage/logs/laravel.log\n";
} else {
    echo "Found " . count($problems) . " issue(s):\n\n";
    foreach ($problems as $i => $problem) {
        echo ($i + 1) . ". " . $problem . "\n";
    }
}

echo "\n=== END DIAGNOSTIC ===\n";
