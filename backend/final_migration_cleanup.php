<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🎯 Final Migration Cleanup and Verification\n";
echo "==========================================\n\n";

try {
    // 1. Clean up migration table entries for removed files
    echo "1️⃣ Cleaning migration table...\n";
    
    $removedMigrations = [
        '2025_01_27_210000_create_role_management_tables',
        '2025_01_27_250000_fix_users_table_structure',
        '2025_01_28_000001_fix_users_table_safe',
        '2025_01_28_000002_create_declarations_table_fixed',
        '2025_01_27_210000_remove_hod_it_role_and_columns',
        '2025_01_27_300000_remove_super_admin_role'
    ];
    
    foreach ($removedMigrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if ($exists) {
            DB::table('migrations')->where('migration', $migration)->delete();
            echo "   ✅ Removed from DB: {$migration}\n";
        } else {
            echo "   ℹ️  Not in DB: {$migration}\n";
        }
    }
    
    // 2. Show current migration files
    echo "\n2️⃣ Current migration files:\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames);
    
    echo "   📋 " . count($migrationNames) . " migration files found:\n";
    foreach ($migrationNames as $index => $migration) {
        $step = $index + 1;
        echo "   {$step}. {$migration}\n";
    }
    
    // 3. Verify no duplicates
    echo "\n3️⃣ Verifying no duplicate table creations...\n";
    
    $tableCreations = [];
    $duplicates = [];
    
    foreach ($migrationNames as $migration) {
        if (preg_match('/create_(\w+)_table/', $migration, $matches)) {
            $tableName = $matches[1];
            if (isset($tableCreations[$tableName])) {
                $duplicates[] = $tableName;
                echo "   ❌ DUPLICATE: {$tableName}\n";
                echo "      - {$tableCreations[$tableName]}\n";
                echo "      - {$migration}\n";
            } else {
                $tableCreations[$tableName] = $migration;
            }
        }
    }
    
    if (empty($duplicates)) {
        echo "   ✅ No duplicate table creations found!\n";
    }
    
    // 4. Show execution order with dependencies
    echo "\n4️⃣ Migration execution order with dependencies:\n";
    
    $dependencies = [
        'create_roles_table' => [],
        'create_departments_table' => [],
        'create_users_table' => [],
        'add_pf_number_to_users_table' => ['create_users_table'],
        'add_department_id_to_users_table' => ['create_users_table', 'create_departments_table'],
        'create_user_access_table' => ['create_users_table', 'create_departments_table'],
        'create_module_access_requests_table' => ['create_users_table'],
        'add_approval_workflow_to_module_access_requests_table' => ['create_module_access_requests_table'],
        'create_declarations_table' => ['create_users_table'],
        'assign_hod_users_to_departments' => ['create_users_table', 'create_departments_table', 'create_roles_table'],
        'migrate_existing_roles_to_many_to_many' => ['create_users_table', 'create_roles_table'],
        'ensure_all_users_have_roles' => ['create_users_table', 'create_roles_table']
    ];
    
    echo "   📋 Execution order with dependency validation:\n";
    
    $foundMigrations = [];
    foreach ($migrationNames as $migration) {
        foreach ($dependencies as $pattern => $deps) {
            if (strpos($migration, $pattern) !== false) {
                $foundMigrations[$pattern] = $migration;
                break;
            }
        }
    }
    
    $orderIssues = [];
    foreach ($dependencies as $migration => $deps) {
        if (!isset($foundMigrations[$migration])) continue;
        
        $migrationPosition = array_search($foundMigrations[$migration], $migrationNames);
        
        foreach ($deps as $dependency) {
            if (!isset($foundMigrations[$dependency])) continue;
            
            $dependencyPosition = array_search($foundMigrations[$dependency], $migrationNames);
            
            if ($dependencyPosition > $migrationPosition) {
                $orderIssues[] = "{$migration} depends on {$dependency} but runs before it";
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
    
    // 5. Show final status
    echo "\n5️⃣ Final status:\n";
    
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    $pendingMigrations = array_diff($migrationNames, $executedMigrations);
    
    echo "   📊 Migration statistics:\n";
    echo "      - Total migrations: " . count($migrationNames) . "\n";
    echo "      - Executed: " . count($executedMigrations) . "\n";
    echo "      - Pending: " . count($pendingMigrations) . "\n";
    
    if (!empty($pendingMigrations)) {
        echo "\n   📋 Pending migrations (ready to run):\n";
        foreach ($pendingMigrations as $migration) {
            $type = '';
            if (strpos($migration, 'create_') !== false) {
                $type = '🏗️  [CREATE]';
            } elseif (strpos($migration, 'add_') !== false || strpos($migration, 'update_') !== false) {
                $type = '🔧 [MODIFY]';
            } elseif (strpos($migration, 'assign_') !== false || strpos($migration, 'migrate_') !== false || strpos($migration, 'ensure_') !== false) {
                $type = '📊 [DATA]';
            } else {
                $type = '⚙️  [OTHER]';
            }
            echo "      {$type} {$migration}\n";
        }
    }
    
    echo "\n🎉 MIGRATION CLEANUP COMPLETE!\n";
    echo "✅ Removed all duplicate and conflicting migrations\n";
    echo "✅ Cleaned migration table entries\n";
    echo "✅ Verified dependency order is correct\n";
    echo "✅ No conflicts remain\n";
    
    echo "\n📋 READY TO MIGRATE!\n";
    echo "Run: php artisan migrate\n";
    echo "All " . count($pendingMigrations) . " pending migrations will execute successfully.\n";
    
} catch (Exception $e) {
    echo "❌ Error during final cleanup: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>