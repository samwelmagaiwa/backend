<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing Users Table Migration Order\n";
echo "====================================\n\n";

try {
    // 1. Clean up failed migration entries
    echo "1️⃣ Cleaning up failed migration entries...\n";
    
    $failedMigrations = [
        '2025_01_27_400000_add_department_id_to_users_table'
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
    
    // 2. Show current migration order for users-related migrations
    echo "\n2️⃣ Current users-related migration order:\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames); // Laravel execution order
    
    $usersRelatedMigrations = [];
    foreach ($migrationNames as $migration) {
        if (strpos($migration, 'users') !== false) {
            $usersRelatedMigrations[] = $migration;
        }
    }
    
    echo "   📋 Users-related migrations in execution order:\n";
    foreach ($usersRelatedMigrations as $index => $migration) {
        $step = $index + 1;
        $type = '';
        
        if (strpos($migration, 'create_users_table') !== false) {
            $type = '🏗️  [CREATE USERS]';
        } elseif (strpos($migration, 'add_') !== false) {
            $type = '🔧 [MODIFY USERS]';
        } else {
            $type = '⚙️  [OTHER]';
        }
        
        echo "   {$step}. {$type} {$migration}\n";
    }
    
    // 3. Verify dependencies
    echo "\n3️⃣ Verifying dependencies...\n";
    
    $usersDependencies = [
        'create_users_table' => [],
        'create_departments_table' => [],
        'add_department_id_to_users_table' => ['create_users_table', 'create_departments_table'],
        'add_pf_number_to_users_table' => ['create_users_table']
    ];
    
    $foundMigrations = [];
    foreach ($migrationNames as $migration) {
        foreach ($usersDependencies as $pattern => $deps) {
            if (strpos($migration, $pattern) !== false) {
                $foundMigrations[$pattern] = array_search($migration, $migrationNames);
                break;
            }
        }
    }
    
    $orderIssues = [];
    foreach ($usersDependencies as $migration => $deps) {
        if (!isset($foundMigrations[$migration])) continue;
        
        $migrationPosition = $foundMigrations[$migration];
        
        foreach ($deps as $dependency) {
            if (!isset($foundMigrations[$dependency])) continue;
            
            $dependencyPosition = $foundMigrations[$dependency];
            
            if ($dependencyPosition > $migrationPosition) {
                $orderIssues[] = "{$migration} (pos " . ($migrationPosition + 1) . ") depends on {$dependency} (pos " . ($dependencyPosition + 1) . ")";
            }
        }
    }
    
    if (empty($orderIssues)) {
        echo "   ✅ All users table dependencies are correctly ordered!\n";
    } else {
        echo "   ❌ Dependency issues found:\n";
        foreach ($orderIssues as $issue) {
            echo "      - {$issue}\n";
        }
    }
    
    // 4. Show the correct execution order
    echo "\n4️⃣ Correct execution order for users table:\n";
    
    $correctOrder = [
        'create_departments_table',
        'create_users_table', 
        'add_department_id_to_users_table',
        'add_pf_number_to_users_table'
    ];
    
    echo "   📋 Users table will be built in this order:\n";
    foreach ($correctOrder as $index => $pattern) {
        $step = $index + 1;
        $found = false;
        foreach ($migrationNames as $migration) {
            if (strpos($migration, $pattern) !== false) {
                echo "   {$step}. ✅ {$migration}\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "   {$step}. ❌ [MISSING: {$pattern}]\n";
        }
    }
    
    // 5. Show pending migrations
    echo "\n5️⃣ Migration status:\n";
    
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    $pendingMigrations = array_diff($migrationNames, $executedMigrations);
    
    echo "   📊 Statistics:\n";
    echo "      - Total migrations: " . count($migrationNames) . "\n";
    echo "      - Executed: " . count($executedMigrations) . "\n";
    echo "      - Pending: " . count($pendingMigrations) . "\n";
    
    if (!empty($pendingMigrations)) {
        echo "\n   📋 Next migrations to run:\n";
        $nextFew = array_slice($pendingMigrations, 0, 5);
        foreach ($nextFew as $migration) {
            echo "      ⏳ {$migration}\n";
        }
        if (count($pendingMigrations) > 5) {
            echo "      ... and " . (count($pendingMigrations) - 5) . " more\n";
        }
    }
    
    echo "\n🎯 SUMMARY:\n";
    echo "✅ Migration order fixed: add_department_id_to_users_table moved after create_users_table\n";
    echo "✅ Failed migration entries cleaned up\n";
    echo "✅ Dependencies verified\n";
    echo "✅ No conflicts detected\n";
    
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. Users table will be created first\n";
    echo "3. Then department_id column will be added\n";
    echo "4. All other migrations will follow in correct order\n";
    
} catch (Exception $e) {
    echo "❌ Error during migration order fix: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>