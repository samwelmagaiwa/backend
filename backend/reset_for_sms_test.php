<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Resetting Request #59 for Automatic SMS Test ===\n\n";

$request = \App\Models\UserAccess::find(59);

if (!$request) {
    echo "❌ Request not found\n";
    exit(1);
}

echo "Current Status:\n";
echo "  - Divisional Status: {$request->divisional_status}\n";
echo "  - SMS to ICT Director: {$request->sms_to_ict_director_status}\n\n";

// Reset to pending divisional approval
$request->update([
        'divisional_status' => null,
        'divisional_director_name' => null,
        'divisional_director_signature_path' => null,
        'divisional_approved_at' => null,
        'divisional_director_comments' => null,
        'sms_to_ict_director_status' => 'pending',
        'sms_sent_to_ict_director_at' => null,
        'updated_at' => now()
    ]);

$request->refresh();

echo "✅ Request Reset Successfully:\n";
echo "  - Divisional Status: " . ($request->divisional_status ?? 'NULL (pending)') . "\n";
echo "  - SMS to ICT Director: {$request->sms_to_ict_director_status}\n\n";

echo "🔔 Now approve the request through the dashboard to test automatic SMS!\n";
echo "   The SMS should automatically be sent to +255617919104\n";
