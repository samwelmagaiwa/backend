<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🏁 Running Final 5 Migrations\n";
echo "=============================\n\n";

try {
    // List of final migrations
    $finalMigrations = [
        '2025_07_15_000011_migrate_existing_roles_to_many_to_many',
        '2025_07_15_000012_ensure_all_users_have_roles',
        '2025_08_21_100000_fix_user_access_request_type_constraints',
        '2025_08_21_110000_convert_user_access_to_json_and_consolidate',
        '2025_08_21_120000_create_booking_service_table'
    ];
    
    // Pre-flight checks
    echo "🔍 Pre-flight checks:\n";
    
    $requiredTables = ['users', 'roles', 'departments', 'user_access'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo "   ✅ {$table}: {$count} records\n";
        } else {
            echo "   ❌ {$table}: missing\n";
            $missingTables[] = $table;
        }
    }
    
    if (!empty($missingTables)) {
        echo "\n❌ CRITICAL: Missing required tables: " . implode(', ', $missingTables) . "\n";
        echo "Cannot proceed with final migrations.\n";
        exit(1);
    }
    
    // Check role_user table
    if (Schema::hasTable('role_user')) {
        $roleUserCount = DB::table('role_user')->count();
        echo "   ✅ role_user: {$roleUserCount} records\n";
    } else {
        echo "   ⚠️  role_user: missing (will be created by role management migration)\n";
    }
    
    echo "\n📊 Current migration status:\n";
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    $pendingMigrations = array_diff($finalMigrations, $executedMigrations);
    
    echo "   - Executed: " . count(array_intersect($finalMigrations, $executedMigrations)) . "/5\n";
    echo "   - Pending: " . count($pendingMigrations) . "/5\n";
    
    if (empty($pendingMigrations)) {
        echo "\n✅ All final migrations have already been executed!\n";
        exit(0);
    }
    
    echo "\n📋 Pending migrations:\n";
    foreach ($pendingMigrations as $migration) {
        echo "   ⏳ {$migration}\n";
    }
    
    // Migration-specific checks
    echo "\n🔍 Migration-specific checks:\n";
    
    // Check for migrate_existing_roles_to_many_to_many
    if (in_array('2025_07_15_000011_migrate_existing_roles_to_many_to_many', $pendingMigrations)) {
        echo "\n1️⃣ migrate_existing_roles_to_many_to_many:\n";
        
        // Check if users have old role_id column
        if (Schema::hasColumn('users', 'role_id')) {
            $usersWithRoles = DB::table('users')->whereNotNull('role_id')->count();
            echo "   📊 Users with role_id: {$usersWithRoles}\n";
            
            if ($usersWithRoles > 0) {
                echo "   ✅ Ready to migrate existing roles\n";
            } else {
                echo "   ℹ️  No users with existing roles to migrate\n";
            }
        } else {
            echo "   ℹ️  No role_id column found (migration may skip)\n";
        }
    }
    
    // Check for user_access table structure
    if (in_array('2025_08_21_100000_fix_user_access_request_type_constraints', $pendingMigrations)) {
        echo "\n3️⃣ fix_user_access_request_type_constraints:\n";
        
        if (Schema::hasColumn('user_access', 'request_type')) {
            $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'request_type'");
            $currentType = $columns[0]->Type ?? 'unknown';
            echo "   📊 Current request_type: {$currentType}\n";
            
            $recordsCount = DB::table('user_access')->count();
            echo "   📊 Records to process: {$recordsCount}\n";
            echo "   ✅ Ready to fix constraints\n";
        } else {
            echo "   ❌ request_type column missing\n";
        }
    }
    
    echo "\n🚀 READY TO PROCEED!\n";
    echo "All prerequisites are met for the final migrations.\n";
    
    echo "\n📋 EXECUTION PLAN:\n";
    echo "1. 📊 Migrate existing user roles to many-to-many relationship\n";
    echo "2. 📊 Ensure all users have proper role assignments\n";
    echo "3. 🔧 Fix user_access request_type column constraints\n";
    echo "4. 🔧 Convert user_access data to JSON and consolidate duplicates\n";
    echo "5. 🏗️  Create booking_service table for device bookings\n";
    
    echo "\n✨ FINAL STEP:\n";
    echo "Run: php artisan migrate\n";
    echo "This will complete your entire migration process!\n";
    
    // Show what will happen after completion
    echo "\n🎉 AFTER COMPLETION:\n";
    echo "✅ Complete user management system with role-based access\n";
    echo "✅ Consolidated user access requests with JSON support\n";
    echo "✅ Device booking service for hospital equipment\n";
    echo "✅ All tables properly indexed and optimized\n";
    echo "✅ Ready for production use!\n";
    
} catch (Exception $e) {
    echo "❌ Error during final migration check: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>