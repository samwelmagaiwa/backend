<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🏁 Final Migration Check\n";
echo "=======================\n\n";

try {
    // List of final pending migrations
    $finalMigrations = [
        '2025_07_15_000011_migrate_existing_roles_to_many_to_many',
        '2025_07_15_000012_ensure_all_users_have_roles',
        '2025_08_21_100000_fix_user_access_request_type_constraints',
        '2025_08_21_110000_convert_user_access_to_json_and_consolidate',
        '2025_08_21_120000_create_booking_service_table'
    ];
    
    echo "📋 Final 5 pending migrations:\n";
    foreach ($finalMigrations as $index => $migration) {
        $step = $index + 1;
        
        $type = '';
        if (strpos($migration, 'create_') !== false) {
            $type = '🏗️  [CREATE]';
        } elseif (strpos($migration, 'migrate_') !== false || strpos($migration, 'ensure_') !== false) {
            $type = '📊 [DATA]';
        } elseif (strpos($migration, 'fix_') !== false || strpos($migration, 'convert_') !== false) {
            $type = '🔧 [MODIFY]';
        } else {
            $type = '⚙️  [OTHER]';
        }
        
        echo "   {$step}. {$type} {$migration}\n";
    }
    
    // Check prerequisites for each migration
    echo "\n🔍 Checking prerequisites for each migration:\n";
    
    // 1. migrate_existing_roles_to_many_to_many
    echo "\n1️⃣ migrate_existing_roles_to_many_to_many:\n";
    $requiredTables1 = ['users', 'roles', 'role_user'];
    $missing1 = [];
    foreach ($requiredTables1 as $table) {
        if (Schema::hasTable($table)) {
            echo "   ✅ {$table} table exists\n";
        } else {
            echo "   ❌ {$table} table missing\n";
            $missing1[] = $table;
        }
    }
    
    if (empty($missing1)) {
        $userCount = DB::table('users')->count();
        $roleCount = DB::table('roles')->count();
        echo "   📊 Data: {$userCount} users, {$roleCount} roles\n";
        echo "   ✅ Ready to run\n";
    } else {
        echo "   ❌ Missing tables: " . implode(', ', $missing1) . "\n";
    }
    
    // 2. ensure_all_users_have_roles
    echo "\n2️⃣ ensure_all_users_have_roles:\n";
    $requiredTables2 = ['users', 'roles', 'role_user'];
    $missing2 = [];
    foreach ($requiredTables2 as $table) {
        if (Schema::hasTable($table)) {
            echo "   ✅ {$table} table exists\n";
        } else {
            echo "   ❌ {$table} table missing\n";
            $missing2[] = $table;
        }
    }
    
    if (empty($missing2)) {
        echo "   ✅ Ready to run\n";
    } else {
        echo "   ❌ Missing tables: " . implode(', ', $missing2) . "\n";
    }
    
    // 3. fix_user_access_request_type_constraints
    echo "\n3️⃣ fix_user_access_request_type_constraints:\n";
    if (Schema::hasTable('user_access')) {
        echo "   ✅ user_access table exists\n";
        
        // Check if request_type column exists
        if (Schema::hasColumn('user_access', 'request_type')) {
            echo "   ✅ request_type column exists\n";
            echo "   ✅ Ready to run\n";
        } else {
            echo "   ❌ request_type column missing\n";
        }
    } else {
        echo "   ❌ user_access table missing\n";
    }
    
    // 4. convert_user_access_to_json_and_consolidate
    echo "\n4️⃣ convert_user_access_to_json_and_consolidate:\n";
    if (Schema::hasTable('user_access')) {
        echo "   ✅ user_access table exists\n";
        
        $recordCount = DB::table('user_access')->count();
        echo "   📊 Data: {$recordCount} user_access records\n";
        echo "   ✅ Ready to run\n";
    } else {
        echo "   ❌ user_access table missing\n";
    }
    
    // 5. create_booking_service_table
    echo "\n5️⃣ create_booking_service_table:\n";
    $requiredTables5 = ['users', 'departments'];
    $missing5 = [];
    foreach ($requiredTables5 as $table) {
        if (Schema::hasTable($table)) {
            echo "   ✅ {$table} table exists (for foreign keys)\n";
        } else {
            echo "   ❌ {$table} table missing\n";
            $missing5[] = $table;
        }
    }
    
    if (empty($missing5)) {
        echo "   ✅ Ready to run\n";
    } else {
        echo "   ❌ Missing tables: " . implode(', ', $missing5) . "\n";
    }
    
    // Overall readiness check
    echo "\n🎯 OVERALL READINESS:\n";
    
    $allReady = empty($missing1) && empty($missing2) && empty($missing5) && 
                Schema::hasTable('user_access');
    
    if ($allReady) {
        echo "   ✅ ALL MIGRATIONS ARE READY TO RUN!\n";
        echo "   🚀 You can safely run: php artisan migrate\n";
    } else {
        echo "   ⚠️  Some migrations may have issues\n";
        echo "   🔧 Check the missing prerequisites above\n";
    }
    
    // Show current database state
    echo "\n📊 CURRENT DATABASE STATE:\n";
    
    $importantTables = ['users', 'roles', 'departments', 'user_access', 'role_user'];
    foreach ($importantTables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo "   ✅ {$table}: {$count} records\n";
        } else {
            echo "   ❌ {$table}: table missing\n";
        }
    }
    
    // Migration execution plan
    echo "\n📋 EXECUTION PLAN:\n";
    echo "   1. 📊 Migrate existing roles to many-to-many relationship\n";
    echo "   2. 📊 Ensure all users have proper role assignments\n";
    echo "   3. 🔧 Fix user_access request type constraints\n";
    echo "   4. 🔧 Convert user_access data to JSON format\n";
    echo "   5. 🏗️  Create booking_service table\n";
    
    echo "\n🎉 FINAL STRETCH!\n";
    echo "You're just 5 migrations away from completion!\n";
    
} catch (Exception $e) {
    echo "❌ Error during final migration check: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>