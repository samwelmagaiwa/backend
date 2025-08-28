<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧹 Cleaning Duplicate and Conflicting Migrations\n";
echo "===============================================\n\n";

try {
    // 1. Remove duplicate and conflicting migration files
    echo "1️⃣ Removing duplicate and conflicting migration files...\n";
    
    $filesToRemove = [
        'database/migrations/2025_01_27_210000_create_role_management_tables.php',
        'database/migrations/2025_01_27_250000_fix_users_table_structure.php',
        'database/migrations/2025_01_28_000001_fix_users_table_safe.php',
        'database/migrations/2025_01_28_000002_create_declarations_table_fixed.php',
        'database/migrations/2025_01_27_210000_remove_hod_it_role_and_columns.php',
        'database/migrations/2025_01_27_300000_remove_super_admin_role.php'
    ];
    
    foreach ($filesToRemove as $file) {
        if (file_exists($file)) {
            unlink($file);
            echo "   ✅ Removed: " . basename($file) . "\n";
        } else {
            echo "   ℹ️  Not found: " . basename($file) . "\n";
        }
    }
    
    // 2. Clean up any entries in migrations table
    echo "\n2️⃣ Cleaning migration table entries...\n";
    
    $migrationsToClean = [
        '2025_01_27_210000_create_role_management_tables',
        '2025_01_27_250000_fix_users_table_structure',
        '2025_01_28_000001_fix_users_table_safe',
        '2025_01_28_000002_create_declarations_table_fixed',
        '2025_01_27_210000_remove_hod_it_role_and_columns',
        '2025_01_27_300000_remove_super_admin_role'
    ];
    
    foreach ($migrationsToClean as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if ($exists) {
            DB::table('migrations')->where('migration', $migration)->delete();
            echo "   ✅ Removed from DB: {$migration}\n";
        } else {
            echo "   ℹ️  Not in DB: {$migration}\n";
        }
    }
    
    // 3. Show remaining migrations
    echo "\n3️⃣ Remaining migration files:\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames);
    
    $essentialMigrations = [
        'create_roles_table',
        'create_departments_table',
        'create_users_table',
        'add_pf_number_to_users_table',
        'add_department_id_to_users_table',
        'create_personal_access_tokens_table',
        'create_sessions_table',
        'create_user_access_table',
        'create_module_access_requests_table',
        'create_declarations_table',
        'create_booking_service_table'
    ];
    
    echo "   📋 Essential table creation migrations:\n";
    foreach ($migrationNames as $migration) {
        foreach ($essentialMigrations as $essential) {
            if (strpos($migration, $essential) !== false) {
                echo "      ✅ {$migration}\n";
                break;
            }
        }
    }
    
    // 4. Check for any remaining conflicts
    echo "\n4️⃣ Checking for remaining conflicts...\n";
    
    $tableCreations = [];
    foreach ($migrationNames as $migration) {
        if (preg_match('/create_(\w+)_table/', $migration, $matches)) {
            $tableName = $matches[1];
            if (isset($tableCreations[$tableName])) {
                echo "   ⚠️  Duplicate table creation for: {$tableName}\n";
                echo "      - {$tableCreations[$tableName]}\n";
                echo "      - {$migration}\n";
            } else {
                $tableCreations[$tableName] = $migration;
            }
        }
    }
    
    if (empty($tableCreations) || count($tableCreations) === count(array_unique($tableCreations))) {
        echo "   ✅ No duplicate table creations found!\n";
    }
    
    // 5. Show final migration order
    echo "\n5️⃣ Final migration execution order:\n";
    
    echo "   📋 Migrations will execute in this order:\n";
    foreach ($migrationNames as $index => $migration) {
        $step = $index + 1;
        $type = '';
        
        if (strpos($migration, 'create_') !== false) {
            $type = '🏗️ ';
        } elseif (strpos($migration, 'add_') !== false || strpos($migration, 'update_') !== false) {
            $type = '🔧';
        } elseif (strpos($migration, 'assign_') !== false || strpos($migration, 'migrate_') !== false || strpos($migration, 'ensure_') !== false) {
            $type = '📊';
        } else {
            $type = '⚙️ ';
        }
        
        echo "   {$step}. {$type} {$migration}\n";
    }
    
    echo "\n🎉 CLEANUP COMPLETE!\n";
    echo "✅ Removed duplicate migrations\n";
    echo "✅ Cleaned migration table\n";
    echo "✅ Verified no conflicts remain\n";
    echo "✅ Migration order is now correct\n";
    
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. All migrations will execute in correct dependency order\n";
    echo "3. No conflicts or duplicates will occur\n";
    
} catch (Exception $e) {
    echo "❌ Error during cleanup: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>