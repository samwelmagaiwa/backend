<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 Checking table structures for foreign key compatibility...\n\n";

try {
    // Check if users table exists
    if (!Schema::hasTable('users')) {
        echo "❌ Users table doesn't exist! This is the root cause.\n";
        echo "The declarations table cannot reference a non-existent users table.\n\n";
        
        echo "🔧 Solution: Create users table first or run migrations in correct order.\n";
        exit(1);
    }
    
    echo "✅ Users table exists\n";
    
    // Get users table structure
    echo "\n📊 Users table structure:\n";
    $usersColumns = DB::select("DESCRIBE users");
    foreach ($usersColumns as $column) {
        echo sprintf("   %-20s %-20s %-10s %-10s %-10s %-20s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key, 
            $column->Default ?? 'NULL', 
            $column->Extra ?? ''
        );
    }
    
    // Check if declarations table exists
    if (Schema::hasTable('declarations')) {
        echo "\n📊 Declarations table structure:\n";
        $declarationsColumns = DB::select("DESCRIBE declarations");
        foreach ($declarationsColumns as $column) {
            echo sprintf("   %-20s %-20s %-10s %-10s %-10s %-20s\n", 
                $column->Field, 
                $column->Type, 
                $column->Null, 
                $column->Key, 
                $column->Default ?? 'NULL', 
                $column->Extra ?? ''
            );
        }
    } else {
        echo "\nℹ️  Declarations table doesn't exist yet (this is expected if migration failed)\n";
    }
    
    // Check users.id column specifically
    $usersIdColumn = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
    if (!empty($usersIdColumn)) {
        $idColumn = $usersIdColumn[0];
        echo "\n🔑 Users.id column details:\n";
        echo "   Type: {$idColumn->Type}\n";
        echo "   Null: {$idColumn->Null}\n";
        echo "   Key: {$idColumn->Key}\n";
        echo "   Extra: {$idColumn->Extra}\n";
        
        // Check if it's the correct type for foreign key reference
        if (strpos(strtolower($idColumn->Type), 'bigint') !== false && 
            strpos(strtolower($idColumn->Extra), 'auto_increment') !== false) {
            echo "   ✅ Correct type for foreign key reference (BIGINT UNSIGNED AUTO_INCREMENT)\n";
        } else {
            echo "   ⚠️  Potential issue: Expected BIGINT UNSIGNED AUTO_INCREMENT\n";
        }
    }
    
    // Check existing foreign keys in the database
    echo "\n🔗 Existing foreign key constraints:\n";
    $foreignKeys = DB::select("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    if (empty($foreignKeys)) {
        echo "   No foreign key constraints found\n";
    } else {
        foreach ($foreignKeys as $fk) {
            echo "   {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
    }
    
    // Check migration order
    echo "\n📋 Migration execution order:\n";
    $migrations = DB::table('migrations')->orderBy('batch', 'asc')->orderBy('migration', 'asc')->get();
    foreach ($migrations as $migration) {
        echo "   Batch {$migration->batch}: {$migration->migration}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nThis might indicate the database connection issue or missing tables.\n";
}
?>