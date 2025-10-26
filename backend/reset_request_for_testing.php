<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Reassigning ICT Director Role and Resetting Request ===\n\n";

// 1. Find the user with email ict.officer@mnh.go.tz
$newIctDirector = \App\Models\User::where('email', 'ict.officer@mnh.go.tz')->first();

if (!$newIctDirector) {
    echo "❌ User with email ict.officer@mnh.go.tz not found\n";
    exit(1);
}

echo "✅ Found user: {$newIctDirector->name} (ID: {$newIctDirector->id})\n";
echo "   Phone: {$newIctDirector->phone}\n";
echo "   Email: {$newIctDirector->email}\n\n";

// 2. Get the ICT Director role
$ictDirectorRole = \App\Models\Role::where('name', 'ict_director')->first();

if (!$ictDirectorRole) {
    echo "❌ ICT Director role not found\n";
    exit(1);
}

echo "✅ Found ICT Director role (ID: {$ictDirectorRole->id})\n\n";

// 3. Remove ICT Director role from old user (ID: 2)
$oldIctDirector = \App\Models\User::find(2);
if ($oldIctDirector && $oldIctDirector->hasRole('ict_director')) {
    $oldIctDirector->roles()->detach($ictDirectorRole->id);
    echo "✅ Removed ICT Director role from: {$oldIctDirector->name}\n";
}

// 4. Assign ICT Director role to new user
if (!$newIctDirector->hasRole('ict_director')) {
    $newIctDirector->roles()->attach($ictDirectorRole->id);
    echo "✅ Assigned ICT Director role to: {$newIctDirector->name}\n\n";
} else {
    echo "ℹ️  User already has ICT Director role\n\n";
}

// 5. Reset request #59 back to hod_approved status
echo "=== Resetting Request #59 ===\n\n";

$request = \App\Models\UserAccess::find(59);
if (!$request) {
    echo "❌ Request #59 not found\n";
    exit(1);
}

echo "📋 Current Status:\n";
echo "   - divisional_status: {$request->divisional_status}\n";
echo "   - sms_to_ict_director_status: {$request->sms_to_ict_director_status}\n\n";

// Reset the request - only update fields that exist
\DB::table('user_access')
    ->where('id', 59)
    ->update([
        'divisional_status' => null,
        'divisional_approved_at' => null,
        'sms_to_ict_director_status' => 'pending',
        'sms_sent_to_ict_director_at' => null,
        'updated_at' => now()
    ]);

echo "✅ Reset request #59 to HOD Approved status\n";
echo "   - divisional_status: NULL (pending divisional approval)\n";
echo "   - sms_to_ict_director_status: pending\n\n";

// 6. Verify the changes
$request->refresh();
echo "📊 New Status:\n";
echo "   - Status: {$request->status}\n";
echo "   - HOD Status: {$request->hod_status}\n";
echo "   - Divisional Status: " . ($request->divisional_status ?: 'NULL (pending)') . "\n";
echo "   - SMS to ICT Director: {$request->sms_to_ict_director_status}\n\n";

echo "=== Ready for Testing ===\n";
echo "✅ You can now approve request #59 as Divisional Director\n";
echo "✅ SMS will be sent to: {$newIctDirector->name} ({$newIctDirector->phone})\n\n";

echo "=== Done ===\n";
