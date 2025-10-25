<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserAccess;
use Illuminate\Support\Facades\DB;

echo "=== REQUEST #49 STRUCTURE ===\n\n";

$request = UserAccess::find(49);

if (!$request) {
    echo "❌ Request not found!\n";
    exit(1);
}

echo "Request Fields:\n";
echo "  ID: {$request->id}\n";
echo "  Staff: {$request->staff_name}\n";
echo "  Status: {$request->status}\n";
echo "  Request Type (JSON): " . json_encode($request->request_type) . "\n";
echo "  Jeeva Access (bool): " . ($request->jeeva_access ? 'true' : 'false') . "\n";
echo "  Wellsoft Access (bool): " . ($request->wellsoft_access ? 'true' : 'false') . "\n";
echo "  Internet Access (bool): " . ($request->internet_access ? 'true' : 'false') . "\n";

echo "\nRaw DB data:\n";
$raw = DB::table('user_access')->where('id', 49)->first();
print_r($raw);

echo "\n=== END ===\n";
