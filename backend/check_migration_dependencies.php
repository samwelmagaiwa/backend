<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Migration Dependencies Analysis\n";
echo "=================================\n\n";

try {
    // Get all migration files
    $migrationFiles = glob('database/migrations/*.php');
    $migrationNames = array_map(function($file) {
        return basename($file, '.php');
    }, $migrationFiles);
    
    sort($migrationNames); // Laravel execution order
    
    echo "📋 All migrations in execution order:\n";
    foreach ($migrationNames as $index => $migration) {
        $position = $index + 1;
        echo sprintf("%2d. %s\n", $position, $migration);
    }
    
    echo "\n🔍 Dependency Analysis:\n";
    
    // Define table creation and modification patterns
    $tableCreations = [];
    $tableModifications = [];
    
    foreach ($migrationNames as $migration) {
        if (preg_match('/create_(\w+)_table/', $migration, $matches)) {
            $tableName = $matches[1];
            $tableCreations[$tableName] = $migration;
        } elseif (preg_match('/(add|modify|update).*?(\w+)_table/', $migration, $matches)) {
            $tableName = $matches[2];
            if (!isset($tableModifications[$tableName])) {
                $tableModifications[$tableName] = [];
            }
            $tableModifications[$tableName][] = $migration;
        }
    }
    
    echo "\n📊 Table Creation Migrations:\n";
    foreach ($tableCreations as $table => $migration) {
        echo "   {$table}: {$migration}\n";
    }
    
    echo "\n🔧 Table Modification Migrations:\n";
    foreach ($tableModifications as $table => $migrations) {
        echo "   {$table}:\n";
        foreach ($migrations as $migration) {
            echo "      - {$migration}\n";
        }
    }
    
    echo "\n⚠️  Potential Order Issues:\n";
    $issues = [];
    
    foreach ($tableModifications as $table => $modifications) {
        $creationMigration = $tableCreations[$table] ?? null;
        
        if (!$creationMigration) {
            $issues[] = "Table '{$table}' has modifications but no creation migration found";
            continue;
        }
        
        $creationPosition = array_search($creationMigration, $migrationNames);
        
        foreach ($modifications as $modification) {
            $modificationPosition = array_search($modification, $migrationNames);
            
            if ($modificationPosition < $creationPosition) {
                $issues[] = "Modification '{$modification}' runs before creation '{$creationMigration}'";
            }
        }
    }
    
    // Check foreign key dependencies
    $foreignKeyDependencies = [
        'module_access_requests' => ['users'],
        'declarations' => ['users'],
        'user_access' => ['users', 'departments'],
        'booking_service' => ['users', 'departments'],
        'user_onboarding' => ['users']
    ];
    
    foreach ($foreignKeyDependencies as $table => $dependencies) {
        $tableCreation = $tableCreations[$table] ?? null;
        if (!$tableCreation) continue;
        
        $tablePosition = array_search($tableCreation, $migrationNames);
        
        foreach ($dependencies as $dependency) {
            $dependencyCreation = $tableCreations[$dependency] ?? null;
            if (!$dependencyCreation) continue;
            
            $dependencyPosition = array_search($dependencyCreation, $migrationNames);
            
            if ($dependencyPosition > $tablePosition) {
                $issues[] = "Table '{$table}' creation runs before its dependency '{$dependency}'";
            }
        }
    }
    
    if (empty($issues)) {
        echo "   ✅ No order issues detected!\n";
    } else {
        foreach ($issues as $issue) {
            echo "   ❌ {$issue}\n";
        }
    }
    
    echo "\n📋 Recommended Migration Order:\n";
    $recommendedOrder = [
        'create_roles_table',
        'create_departments_table', 
        'create_users_table',
        'add_pf_number_to_users_table',
        'fix_users_table_structure',
        'add_department_id_to_users_table',
        'create_user_onboarding_table',
        'create_declarations_table',
        'create_user_access_table',
        'create_module_access_requests_table',
        'add_approval_workflow_to_module_access_requests_table',
        'create_booking_service_table'
    ];
    
    echo "\n   Ideal order:\n";
    foreach ($recommendedOrder as $index => $pattern) {
        $position = $index + 1;
        $found = false;
        foreach ($migrationNames as $migration) {
            if (strpos($migration, $pattern) !== false) {
                echo "   {$position}. {$migration}\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "   {$position}. [MISSING: {$pattern}]\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error during analysis: " . $e->getMessage() . "\n";
}
?>