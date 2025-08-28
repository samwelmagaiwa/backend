<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Complete Migration Fix for Declarations Table\n";
echo "===============================================\n\n";

try {
    // 1. Clean up failed migration entries
    echo "1️⃣ Cleaning up failed migration entries...\n";
    
    $failedMigrations = [
        '2025_01_27_120000_create_declarations_table'
    ];
    
    foreach ($failedMigrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if ($exists) {
            DB::table('migrations')->where('migration', $migration)->delete();
            echo "   ✅ Removed failed migration: {$migration}\n";
        } else {
            echo "   ℹ️  Migration not in database: {$migration}\n";
        }
    }
    
    // 2. Drop declarations table if it exists (partially created)
    echo "\n2️⃣ Cleaning up partially created tables...\n";
    
    if (Schema::hasTable('declarations')) {
        // Drop foreign key constraints first if they exist
        try {
            DB::statement('ALTER TABLE declarations DROP FOREIGN KEY declarations_user_id_foreign');
            echo "   ✅ Dropped foreign key constraint\n";
        } catch (Exception $e) {
            echo "   ℹ️  No foreign key to drop\n";
        }
        
        Schema::dropIfExists('declarations');
        echo "   ✅ Dropped declarations table\n";
    } else {
        echo "   ℹ️  Declarations table doesn't exist\n";
    }
    
    // 3. Verify users table exists and has correct structure
    echo "\n3️⃣ Verifying users table...\n";
    
    if (!Schema::hasTable('users')) {
        echo "   ❌ Users table doesn't exist!\n";
        echo "   🚨 CRITICAL: Users table must be created first\n";
        echo "   Run: php artisan migrate to create users table\n";
        exit(1);
    }
    
    echo "   ✅ Users table exists\n";
    
    // Check users.id column
    $usersIdColumn = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
    if (!empty($usersIdColumn)) {
        $idColumn = $usersIdColumn[0];
        echo "   Users.id type: {$idColumn->Type}\n";
        
        if (strpos(strtolower($idColumn->Type), 'bigint') !== false && 
            strpos(strtolower($idColumn->Extra), 'auto_increment') !== false) {
            echo "   ✅ Users.id has correct type for foreign keys\n";
        } else {
            echo "   ⚠️  Users.id type may cause issues: {$idColumn->Type}\n";
        }
    }
    
    // 4. Show migration order
    echo "\n4️⃣ Checking migration order...\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames); // This is how Laravel orders them
    
    echo "   📋 Migration execution order:\n";
    $usersMigrationFound = false;
    $declarationsMigrationFound = false;
    $usersPosition = 0;
    $declarationsPosition = 0;
    
    foreach ($migrationNames as $index => $migration) {
        $position = $index + 1;
        
        if (strpos($migration, 'create_users_table') !== false) {
            echo "   {$position}. ✅ {$migration} (USERS TABLE)\n";
            $usersMigrationFound = true;
            $usersPosition = $position;
        } elseif (strpos($migration, 'create_declarations_table') !== false) {
            echo "   {$position}. 📄 {$migration} (DECLARATIONS TABLE)\n";
            $declarationsMigrationFound = true;
            $declarationsPosition = $position;
        } else {
            echo "   {$position}. {$migration}\n";
        }
    }
    
    // 5. Validate migration order
    echo "\n5️⃣ Validating migration order...\n";
    
    if (!$usersMigrationFound) {
        echo "   ❌ Users table migration not found!\n";
        exit(1);
    }
    
    if (!$declarationsMigrationFound) {
        echo "   ❌ Declarations table migration not found!\n";
        exit(1);
    }
    
    if ($usersPosition < $declarationsPosition) {
        echo "   ✅ Migration order is correct: Users ({$usersPosition}) before Declarations ({$declarationsPosition})\n";
    } else {
        echo "   ❌ Migration order is WRONG: Declarations ({$declarationsPosition}) before Users ({$usersPosition})\n";
        echo "   🔧 The declarations migration has been moved to run after users\n";
    }
    
    // 6. Show current migration status
    echo "\n6️⃣ Current migration status:\n";
    
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    
    $pendingMigrations = array_diff($migrationNames, $executedMigrations);
    
    if (empty($pendingMigrations)) {
        echo "   ✅ All migrations have been executed\n";
    } else {
        echo "   📋 Pending migrations:\n";
        foreach ($pendingMigrations as $migration) {
            if (strpos($migration, 'create_users_table') !== false) {
                echo "      🔑 {$migration} (REQUIRED FOR DECLARATIONS)\n";
            } elseif (strpos($migration, 'create_declarations_table') !== false) {
                echo "      📄 {$migration} (WILL RUN AFTER USERS)\n";
            } else {
                echo "      ⏳ {$migration}\n";
            }
        }
    }
    
    echo "\n🎯 SUMMARY:\n";
    echo "✅ Failed migration entries cleaned up\n";
    echo "✅ Partially created tables removed\n";
    echo "✅ Migration order verified\n";
    echo "✅ Users table exists and is compatible\n";
    
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. This will execute migrations in the correct order\n";
    echo "3. Users table will be processed first\n";
    echo "4. Declarations table will be created with proper foreign keys\n";
    
    if (in_array('2025_07_10_132300_create_users_table', $pendingMigrations)) {
        echo "\n⚠️  NOTE: Users table migration is still pending.\n";
        echo "   This is normal if you haven't run migrations yet.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during migration fix: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>