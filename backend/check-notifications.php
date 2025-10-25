<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== NOTIFICATIONS CHECK ===\n\n";

try {
    $notifications = DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "📊 Total notifications found: " . $notifications->count() . "\n\n";
    
    if ($notifications->isEmpty()) {
        echo "❌ No notifications found in database!\n";
    } else {
        foreach ($notifications as $notification) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "ID: {$notification->id}\n";
            echo "Recipient ID: {$notification->recipient_id}\n";
            echo "Type: {$notification->type}\n";
            echo "Title: {$notification->title}\n";
            echo "Message: {$notification->message}\n";
            echo "Read at: " . ($notification->read_at ?? 'Unread') . "\n";
            echo "Created at: {$notification->created_at}\n";
            
            if ($notification->data) {
                $data = json_decode($notification->data, true);
                echo "\nData:\n";
                foreach ($data as $key => $value) {
                    echo "  - {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
                }
            }
            echo "\n";
        }
    }
    
    // Check specific request #49
    echo "\n=== REQUEST #49 NOTIFICATIONS ===\n\n";
    $req49Notifications = DB::table('notifications')
        ->where('access_request_id', 49)
        ->get();
    
    if ($req49Notifications->isEmpty()) {
        echo "❌ No notifications found for Request #49\n";
    } else {
        echo "✅ Found {$req49Notifications->count()} notification(s) for Request #49\n";
        foreach ($req49Notifications as $notif) {
            echo "  - ID: {$notif->id}, Recipient: {$notif->recipient_id}, Type: {$notif->type}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo "\n=== END ===\n";
