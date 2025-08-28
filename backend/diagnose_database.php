<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 Database Diagnostic Report\n";
echo "============================\n\n";

try {
    // Database connection info
    $database = DB::connection()->getDatabaseName();
    echo "📊 Database: {$database}\n";
    echo "🔗 Connection: " . DB::connection()->getName() . "\n\n";
    
    // Check all tables
    echo "📋 Existing Tables:\n";
    $tables = DB::select("SHOW TABLES");
    $tableKey = "Tables_in_{$database}";
    
    if (empty($tables)) {
        echo "   ❌ No tables found in database!\n\n";
    } else {
        foreach ($tables as $table) {
            echo "   ✅ " . $table->$tableKey . "\n";
        }
        echo "\n";
    }
    
    // Check users table specifically
    echo "👤 Users Table Analysis:\n";
    if (Schema::hasTable('users')) {
        echo "   ✅ Users table exists\n";
        
        $usersColumns = DB::select("DESCRIBE users");
        echo "   📊 Columns:\n";
        foreach ($usersColumns as $column) {
            $nullable = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
            $key = $column->Key ? " [{$column->Key}]" : '';
            echo "      {$column->Field}: {$column->Type} {$nullable}{$key}\n";
        }
        
        // Check users.id specifically
        $idColumn = collect($usersColumns)->firstWhere('Field', 'id');
        if ($idColumn) {
            echo "\n   🔑 ID Column Details:\n";
            echo "      Type: {$idColumn->Type}\n";
            echo "      Key: {$idColumn->Key}\n";
            echo "      Extra: {$idColumn->Extra}\n";
            
            // Validate for foreign key compatibility
            $isCompatible = (
                strpos(strtolower($idColumn->Type), 'bigint') !== false &&
                strpos(strtolower($idColumn->Extra), 'auto_increment') !== false &&
                $idColumn->Key === 'PRI'
            );
            
            if ($isCompatible) {
                echo "      ✅ Compatible for foreign key references\n";
            } else {
                echo "      ❌ NOT compatible for foreign key references\n";
                echo "         Expected: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY\n";
            }
        }
        
        // Count users
        $userCount = DB::table('users')->count();
        echo "\n   👥 Total users: {$userCount}\n";
        
    } else {
        echo "   ❌ Users table does NOT exist\n";
        echo "   🚨 This is the root cause of the foreign key error!\n";
    }
    
    // Check declarations table
    echo "\n📄 Declarations Table Analysis:\n";
    if (Schema::hasTable('declarations')) {
        echo "   ✅ Declarations table exists\n";
        
        $declarationsColumns = DB::select("DESCRIBE declarations");
        echo "   📊 Columns:\n";
        foreach ($declarationsColumns as $column) {
            $nullable = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
            $key = $column->Key ? " [{$column->Key}]" : '';
            echo "      {$column->Field}: {$column->Type} {$nullable}{$key}\n";
        }
        
        // Count declarations
        $declarationCount = DB::table('declarations')->count();
        echo "\n   📄 Total declarations: {$declarationCount}\n";
        
    } else {
        echo "   ❌ Declarations table does NOT exist\n";
    }
    
    // Check foreign key constraints
    echo "\n🔗 Foreign Key Constraints:\n";
    $foreignKeys = DB::select("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME,
            DELETE_RULE,
            UPDATE_RULE
        FROM information_schema.REFERENTIAL_CONSTRAINTS rc
        JOIN information_schema.KEY_COLUMN_USAGE kcu 
            ON rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
        WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
    ");
    
    if (empty($foreignKeys)) {
        echo "   ℹ️  No foreign key constraints found\n";
    } else {
        foreach ($foreignKeys as $fk) {
            echo "   🔗 {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
            echo "      Constraint: {$fk->CONSTRAINT_NAME}\n";
            echo "      On Delete: {$fk->DELETE_RULE}, On Update: {$fk->UPDATE_RULE}\n\n";
        }
    }
    
    // Check migration status
    echo "🚀 Migration Status:\n";
    if (Schema::hasTable('migrations')) {
        $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
        
        if ($migrations->isEmpty()) {
            echo "   ℹ️  No migrations recorded\n";
        } else {
            $currentBatch = null;
            foreach ($migrations as $migration) {
                if ($currentBatch !== $migration->batch) {
                    $currentBatch = $migration->batch;
                    echo "\n   📦 Batch {$currentBatch}:\n";
                }
                echo "      ✅ {$migration->migration}\n";
            }
        }
        
        // Check for failed migrations
        $failedMigrations = [
            '2025_01_27_120000_create_declarations_table',
            '2025_01_28_000000_fix_users_table_columns'
        ];
        
        echo "\n   🔍 Checking for problematic migrations:\n";
        foreach ($failedMigrations as $migration) {
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            if ($exists) {
                echo "      ⚠️  Found: {$migration} (may need cleanup)\n";
            } else {
                echo "      ✅ Clean: {$migration}\n";
            }
        }
        
    } else {
        echo "   ❌ Migrations table does NOT exist\n";
    }
    
    // Recommendations
    echo "\n💡 Recommendations:\n";
    
    if (!Schema::hasTable('users')) {
        echo "   🚨 CRITICAL: Create users table first before any other migrations\n";
        echo "      Run: php artisan migrate:fresh --seed\n";
    } elseif (Schema::hasTable('declarations')) {
        echo "   ✅ Both tables exist - check foreign key constraints\n";
    } else {
        echo "   🔧 Run the foreign key fix script: php fix_foreign_key_issue.php\n";
    }
    
    echo "\n📋 Summary:\n";
    echo "   Tables: " . count($tables) . "\n";
    echo "   Foreign Keys: " . count($foreignKeys) . "\n";
    echo "   Users Table: " . (Schema::hasTable('users') ? '✅' : '❌') . "\n";
    echo "   Declarations Table: " . (Schema::hasTable('declarations') ? '✅' : '❌') . "\n";
    
} catch (Exception $e) {
    echo "❌ Diagnostic Error: " . $e->getMessage() . "\n";
    echo "\nThis indicates a serious database connection or configuration issue.\n";
    echo "Check your .env file and database server status.\n";
}
?>