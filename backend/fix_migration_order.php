<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing migration order issue...\n\n";

try {
    // 1. Remove the failed migration from the migrations table
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
            echo "   ℹ️  Migration not found in database: {$migration}\n";
        }
    }
    
    // 2. Drop the declarations table if it was partially created
    echo "\n2️⃣ Cleaning up partially created tables...\n";
    
    if (Schema::hasTable('declarations')) {
        Schema::dropIfExists('declarations');
        echo "   ✅ Dropped partially created declarations table\n";
    } else {
        echo "   ℹ️  Declarations table doesn't exist\n";
    }
    
    // 3. Check if users table exists
    echo "\n3️⃣ Checking users table status...\n";
    
    if (Schema::hasTable('users')) {
        echo "   ✅ Users table exists\n";
        
        // Check users.id column type
        $usersIdColumn = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
        if (!empty($usersIdColumn)) {
            $idColumn = $usersIdColumn[0];
            echo "   Users.id type: {$idColumn->Type}\n";
            
            if (strpos(strtolower($idColumn->Type), 'bigint') !== false) {
                echo "   ✅ Users.id has correct BIGINT type for foreign keys\n";
            } else {
                echo "   ⚠️  Users.id type may cause foreign key issues\n";
            }
        }
    } else {
        echo "   ❌ Users table doesn't exist - this will cause foreign key errors\n";
        echo "   The users table migration needs to run first!\n";
    }
    
    // 4. Show current migration status
    echo "\n4️⃣ Current migration status:\n";
    
    if (Schema::hasTable('migrations')) {
        $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
        
        if ($migrations->isEmpty()) {
            echo "   ℹ️  No migrations recorded in database\n";
        } else {
            echo "   📋 Executed migrations:\n";
            $currentBatch = null;
            foreach ($migrations as $migration) {
                if ($currentBatch !== $migration->batch) {
                    $currentBatch = $migration->batch;
                    echo "\n   Batch {$currentBatch}:\n";
                }
                echo "      ✅ {$migration->migration}\n";
            }
        }
    }
    
    // 5. Check which migration files exist but haven't run
    echo "\n5️⃣ Pending migrations:\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    
    $pendingMigrations = [];
    foreach ($migrationFiles as $file) {
        $filename = basename($file, '.php');
        if (!in_array($filename, $executedMigrations)) {
            $pendingMigrations[] = $filename;
        }
    }
    
    if (empty($pendingMigrations)) {
        echo "   ✅ No pending migrations\n";
    } else {
        echo "   📋 Pending migrations (in order they will run):\n";
        sort($pendingMigrations); // Show in alphabetical order (how Laravel runs them)
        foreach ($pendingMigrations as $migration) {
            echo "      ⏳ {$migration}\n";
        }
    }
    
    echo "\n🎯 SOLUTION:\n";
    echo "The issue is that the declarations migration is trying to run before the users migration.\n";
    echo "Laravel runs migrations in alphabetical order by filename.\n\n";
    
    echo "📋 Recommended actions:\n";
    echo "1. The failed migration has been cleaned up\n";
    echo "2. Run: php artisan migrate\n";
    echo "3. This will run migrations in the correct order:\n";
    echo "   - First: users table (2025_07_10_132300_create_users_table)\n";
    echo "   - Then: declarations table (using the fixed version)\n\n";
    
    if (!Schema::hasTable('users')) {
        echo "⚠️  IMPORTANT: If users table still doesn't exist after migration,\n";
        echo "   you may need to run: php artisan migrate:fresh --seed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during cleanup: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>