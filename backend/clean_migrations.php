<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧹 Cleaning up migration table...\n\n";

try {
    // Remove the failed migration from migrations table
    $failedMigration = '2025_01_28_000000_fix_users_table_columns';
    
    $exists = DB::table('migrations')
        ->where('migration', $failedMigration)
        ->exists();
    
    if ($exists) {
        DB::table('migrations')
            ->where('migration', $failedMigration)
            ->delete();
        echo "✅ Removed failed migration: {$failedMigration}\n";
    } else {
        echo "ℹ️  Migration {$failedMigration} not found in migrations table\n";
    }
    
    // Show current migrations
    echo "\n📋 Current migrations in database:\n";
    $migrations = DB::table('migrations')->orderBy('batch')->get();
    foreach ($migrations as $migration) {
        echo "   Batch {$migration->batch}: {$migration->migration}\n";
    }
    
    echo "\n✅ Migration table cleaned!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>