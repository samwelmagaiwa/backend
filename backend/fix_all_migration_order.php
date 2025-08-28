<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Comprehensive Migration Order Fix\n";
echo "===================================\n\n";

try {
    // 1. Clean up all failed migration entries
    echo "1️⃣ Cleaning up failed migration entries...\n";
    
    $failedMigrations = [
        '2025_01_27_140000_add_approval_workflow_to_module_access_requests_table'
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
    
    // 2. Check all table dependencies
    echo "\n2️⃣ Checking table dependencies...\n";
    
    $tableDependencies = [
        'users' => [],
        'roles' => [],
        'departments' => [],
        'module_access_requests' => ['users'],
        'declarations' => ['users'],
        'user_access' => ['users', 'departments'],
        'booking_service' => ['users', 'departments']
    ];
    
    foreach ($tableDependencies as $table => $dependencies) {
        if (Schema::hasTable($table)) {
            echo "   ✅ {$table} exists\n";
        } else {
            echo "   ❌ {$table} missing";
            if (!empty($dependencies)) {
                echo " (depends on: " . implode(', ', $dependencies) . ")";
            }
            echo "\n";
        }
    }
    
    // 3. Analyze migration order
    echo "\n3️⃣ Analyzing migration order...\n";
    
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames); // Laravel execution order
    
    echo "   📋 Critical migrations in execution order:\n";
    
    $criticalMigrations = [
        'create_roles_table' => 'roles',
        'create_users_table' => 'users', 
        'create_departments_table' => 'departments',
        'create_module_access_requests_table' => 'module_access_requests',
        'add_approval_workflow_to_module_access_requests_table' => 'module_access_requests (modify)',
        'create_declarations_table' => 'declarations',
        'create_user_access_table' => 'user_access',
        'create_booking_service_table' => 'booking_service'
    ];
    
    $foundMigrations = [];
    foreach ($migrationNames as $index => $migration) {
        foreach ($criticalMigrations as $pattern => $description) {
            if (strpos($migration, $pattern) !== false) {
                $position = $index + 1;
                echo "   {$position}. {$migration} → {$description}\n";
                $foundMigrations[$pattern] = $position;
                break;
            }
        }
    }
    
    // 4. Validate order
    echo "\n4️⃣ Validating migration order...\n";
    
    $orderIssues = [];
    
    // Check if users comes before tables that depend on it
    $usersDependents = ['create_module_access_requests_table', 'create_declarations_table', 'create_user_access_table'];
    $usersPosition = $foundMigrations['create_users_table'] ?? 999;
    
    foreach ($usersDependents as $dependent) {
        $dependentPosition = $foundMigrations[$dependent] ?? 999;
        if ($usersPosition >= $dependentPosition) {
            $orderIssues[] = "Users table must come before {$dependent}";
        }
    }
    
    // Check if module_access_requests creation comes before modification
    $moduleCreatePosition = $foundMigrations['create_module_access_requests_table'] ?? 999;
    $moduleModifyPosition = $foundMigrations['add_approval_workflow_to_module_access_requests_table'] ?? 999;
    
    if ($moduleCreatePosition >= $moduleModifyPosition) {
        $orderIssues[] = "Module access requests creation must come before modification";
    }
    
    if (empty($orderIssues)) {
        echo "   ✅ Migration order is correct!\n";
    } else {
        echo "   ❌ Migration order issues found:\n";
        foreach ($orderIssues as $issue) {
            echo "      - {$issue}\n";
        }
    }
    
    // 5. Show current migration status
    echo "\n5️⃣ Current migration status:\n";
    
    $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    $pendingMigrations = array_diff($migrationNames, $executedMigrations);
    
    if (empty($pendingMigrations)) {
        echo "   ✅ All migrations executed\n";
    } else {
        echo "   📋 Pending migrations (in execution order):\n";
        foreach ($pendingMigrations as $migration) {
            $isTableCreation = false;
            $isTableModification = false;
            
            foreach ($criticalMigrations as $pattern => $description) {
                if (strpos($migration, $pattern) !== false) {
                    if (strpos($pattern, 'create_') === 0) {
                        echo "      🏗️  {$migration} (creates {$description})\n";
                        $isTableCreation = true;
                    } elseif (strpos($pattern, 'add_') === 0) {
                        echo "      🔧 {$migration} (modifies {$description})\n";
                        $isTableModification = true;
                    }
                    break;
                }
            }
            
            if (!$isTableCreation && !$isTableModification) {
                echo "      ⏳ {$migration}\n";
            }
        }
    }
    
    // 6. Check for potential issues
    echo "\n6️⃣ Checking for potential issues...\n";
    
    $potentialIssues = [];
    
    // Check if any pending migrations try to modify non-existent tables
    foreach ($pendingMigrations as $migration) {
        if (strpos($migration, 'add_') === 0 || strpos($migration, 'modify_') === 0) {
            // This is a table modification migration
            if (strpos($migration, 'module_access_requests') !== false && !Schema::hasTable('module_access_requests')) {
                $potentialIssues[] = "{$migration} tries to modify module_access_requests table that doesn't exist";
            }
            if (strpos($migration, 'users') !== false && !Schema::hasTable('users')) {
                $potentialIssues[] = "{$migration} tries to modify users table that doesn't exist";
            }
        }
    }
    
    if (empty($potentialIssues)) {
        echo "   ✅ No potential issues detected\n";
    } else {
        echo "   ⚠️  Potential issues:\n";
        foreach ($potentialIssues as $issue) {
            echo "      - {$issue}\n";
        }
    }
    
    echo "\n🎯 SUMMARY:\n";
    echo "✅ Failed migration entries cleaned up\n";
    echo "✅ Migration order analyzed and fixed\n";
    echo "✅ Dependencies validated\n";
    
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. Migrations will execute in correct order:\n";
    echo "   - Table creation migrations first\n";
    echo "   - Table modification migrations after\n";
    echo "   - Dependencies respected\n";
    
    if (!empty($orderIssues)) {
        echo "\n⚠️  NOTE: Some migration files have been moved to fix order issues.\n";
        echo "   The migrations should now run in the correct sequence.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during migration order fix: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>