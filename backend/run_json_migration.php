<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

try {
    echo "=== Running JSON Migration ===\n\n";
    
    // Run the specific migration
    echo "Running migration: 2025_08_21_110000_convert_user_access_to_json_and_consolidate\n";
    
    $exitCode = Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_08_21_110000_convert_user_access_to_json_and_consolidate.php',
        '--force' => true
    ]);
    
    echo Artisan::output();
    
    if ($exitCode === 0) {
        echo "\n✅ Migration completed successfully!\n";
        
        // Verify the changes
        echo "\nVerifying database structure...\n";
        $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field IN ('request_type', 'purpose')");
        
        foreach ($columns as $column) {
            echo "  {$column->Field}: {$column->Type}\n";
        }
        
        // Check sample data
        echo "\nSample data:\n";
        $samples = DB::table('user_access')->limit(3)->get(['id', 'pf_number', 'request_type', 'purpose']);
        foreach ($samples as $sample) {
            echo "  ID {$sample->id}: PF {$sample->pf_number} - {$sample->request_type}\n";
        }
        
    } else {
        echo "\n❌ Migration failed with exit code: $exitCode\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}