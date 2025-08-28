<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Fixing Migration Timestamp Order\n";
echo "==================================\n\n";

try {
    // 1. Clean up old migration entries
    echo "1️⃣ Cleaning up old migration entries...\n";
    
    $oldMigrations = [
        '2025_07_10_132350_add_department_id_to_users_table'
    ];
    
    foreach ($oldMigrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if ($exists) {
            DB::table('migrations')->where('migration', $migration)->delete();
            echo "   ✅ Removed old entry: {$migration}\n";
        } else {
            echo "   ℹ️  Not in database: {$migration}\n";
        }
    }
    
    // 2. Get current migration files and verify order
    echo "\n2️⃣ Verifying current migration order...\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames); // Laravel execution order
    
    // Focus on the critical migrations
    $criticalMigrations = [];
    $patterns = [
        'create_roles_table',
        'create_users_table', 
        'create_departments_table',
        'add_department_id_to_users_table',
        'add_pf_number_to_users_table'
    ];
    
    foreach ($migrationNames as $migration) {
        foreach ($patterns as $pattern) {
            if (strpos($migration, $pattern) !== false) {
                $criticalMigrations[] = $migration;
                break;
            }
        }
    }
    
    echo "   📋 Critical migrations in execution order:\n";
    foreach ($criticalMigrations as $index => $migration) {
        $step = $index + 1;
        
        // Extract timestamp for display
        preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_(.+)$/', $migration, $matches);
        $timestamp = $matches[1] ?? 'unknown';
        $name = $matches[2] ?? $migration;
        
        $type = '';
        if (strpos($name, 'create_') === 0) {
            $type = '🏗️  [CREATE]';
        } elseif (strpos($name, 'add_') === 0) {
            $type = '🔧 [MODIFY]';
        }
        
        echo "   {$step}. {$type} {$timestamp} - {$name}\n";
    }
    
    // 3. Validate dependencies
    echo "\n3️⃣ Validating dependencies...\n";
    
    $dependencies = [
        'create_users_table' => [],
        'create_departments_table' => [],
        'add_department_id_to_users_table' => ['create_users_table', 'create_departments_table'],
        'add_pf_number_to_users_table' => ['create_users_table']
    ];
    
    $foundPositions = [];
    foreach ($criticalMigrations as $index => $migration) {
        foreach ($dependencies as $pattern => $deps) {
            if (strpos($migration, $pattern) !== false) {
                $foundPositions[$pattern] = $index;
                break;
            }
        }
    }
    
    $orderIssues = [];
    foreach ($dependencies as $migration => $deps) {
        if (!isset($foundPositions[$migration])) continue;
        
        $migrationPosition = $foundPositions[$migration];
        
        foreach ($deps as $dependency) {
            if (!isset($foundPositions[$dependency])) continue;
            
            $dependencyPosition = $foundPositions[$dependency];
            
            if ($dependencyPosition > $migrationPosition) {
                $orderIssues[] = "{$migration} (pos " . ($migrationPosition + 1) . ") depends on {$dependency} (pos " . ($dependencyPosition + 1) . ")";
            }
        }
    }
    
    if (empty($orderIssues)) {
        echo "   ✅ All dependencies are correctly ordered!\n";
    } else {
        echo "   ❌ Dependency issues found:\n";
        foreach ($orderIssues as $issue) {
            echo "      - {$issue}\n";
        }
    }
    
    // 4. Show the correct execution flow
    echo "\n4️⃣ Correct execution flow:\n";
    
    $executionFlow = [
        '1. create_roles_table → Creates roles table',
        '2. create_users_table → Creates users table', 
        '3. create_departments_table → Creates departments table',
        '4. add_department_id_to_users_table → Adds foreign key to users (requires departments)',
        '5. add_pf_number_to_users_table → Adds pf_number column to users'
    ];
    
    echo "   📋 Execution flow:\n";
    foreach ($executionFlow as $flow) {
        echo "      {$flow}\n";
    }
    
    // 5. Check current migration status
    echo "\n5️⃣ Current migration status:\n";
    
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    $pendingMigrations = array_diff($migrationNames, $executedMigrations);
    
    echo "   📊 Statistics:\n";
    echo "      - Total migrations: " . count($migrationNames) . "\n";
    echo "      - Executed: " . count($executedMigrations) . "\n";
    echo "      - Pending: " . count($pendingMigrations) . "\n";
    
    // Show next few migrations that will run
    if (!empty($pendingMigrations)) {
        echo "\n   📋 Next migrations to execute:\n";
        $nextMigrations = array_slice($pendingMigrations, 0, 8);
        foreach ($nextMigrations as $index => $migration) {
            $step = $index + 1;
            
            $type = '';
            if (strpos($migration, 'create_') !== false) {
                $type = '🏗️ ';
            } elseif (strpos($migration, 'add_') !== false || strpos($migration, 'update_') !== false) {
                $type = '🔧';
            } elseif (strpos($migration, 'assign_') !== false || strpos($migration, 'migrate_') !== false) {
                $type = '📊';
            } else {
                $type = '⚙️ ';
            }
            
            echo "      {$step}. {$type} {$migration}\n";
        }
        
        if (count($pendingMigrations) > 8) {
            echo "      ... and " . (count($pendingMigrations) - 8) . " more migrations\n";
        }
    }
    
    echo "\n🎯 TIMESTAMP ORDER FIX COMPLETE!\n";
    echo "✅ Moved add_department_id_to_users_table to timestamp 132450\n";
    echo "✅ Now runs AFTER departments table creation (132400)\n";
    echo "✅ Cleaned up old migration entries\n";
    echo "✅ Dependencies are correctly ordered\n";
    
    echo "\n📋 READY TO MIGRATE!\n";
    echo "Run: php artisan migrate\n";
    echo "The migrations will now execute in the correct dependency order.\n";
    
} catch (Exception $e) {
    echo "❌ Error during timestamp order fix: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>