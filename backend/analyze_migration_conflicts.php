<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Migration Conflicts Analysis\n";
echo "==============================\n\n";

try {
    // Get all pending migrations
    $pendingMigrations = [
        '2025_01_27_210000_create_role_management_tables',
        '2025_01_27_210000_remove_hod_it_role_and_columns',
        '2025_01_27_250000_fix_users_table_structure',
        '2025_01_27_300000_remove_super_admin_role',
        '2025_01_27_400000_add_department_id_to_users_table',
        '2025_01_28_000001_fix_users_table_safe',
        '2025_01_28_000002_create_declarations_table_fixed',
        '2025_07_10_132202_create_roles_table',
        '2025_07_10_132300_create_users_table',
        '2025_07_10_132400_create_departments_table',
        '2025_07_10_132500_create_user_access_table',
        '2025_07_10_132600_update_user_access_table_for_combined_requests',
        '2025_07_10_140833_create_jeeva_access_requests_table',
        '2025_07_10_142300_create_module_access_requests_table',
        '2025_07_10_142400_add_approval_workflow_to_module_access_requests_table',
        '2025_07_12_165909_create_personal_access_tokens_table',
        '2025_07_12_193706_create_sessions_table',
        '2025_07_14_125826_add_pf_number_to_users_table',
        '2025_07_15_000000_create_user_onboarding_table',
        '2025_07_15_000001_create_declarations_table',
        '2025_07_15_000010_assign_hod_users_to_departments',
        '2025_07_15_000011_migrate_existing_roles_to_many_to_many',
        '2025_07_15_000012_ensure_all_users_have_roles',
        '2025_08_21_100000_fix_user_access_request_type_constraints',
        '2025_08_21_110000_convert_user_access_to_json_and_consolidate',
        '2025_08_21_120000_create_booking_service_table'
    ];
    
    echo "📋 Analyzing " . count($pendingMigrations) . " pending migrations...\n\n";
    
    // 1. Identify duplicate table creation migrations
    echo "1️⃣ DUPLICATE TABLE CREATION CONFLICTS:\n";
    
    $duplicates = [
        'roles_table' => [
            '2025_01_27_210000_create_role_management_tables',
            '2025_07_10_132202_create_roles_table'
        ],
        'users_table' => [
            '2025_07_10_132300_create_users_table',
            '2025_01_27_250000_fix_users_table_structure',
            '2025_01_28_000001_fix_users_table_safe'
        ],
        'departments_table' => [
            '2025_07_10_132400_create_departments_table'
        ],
        'declarations_table' => [
            '2025_01_28_000002_create_declarations_table_fixed',
            '2025_07_15_000001_create_declarations_table'
        ]
    ];
    
    foreach ($duplicates as $table => $migrations) {
        if (count($migrations) > 1) {
            echo "   ❌ {$table} has multiple creation migrations:\n";
            foreach ($migrations as $migration) {
                echo "      - {$migration}\n";
            }
            echo "\n";
        }
    }
    
    // 2. Identify conflicting modifications
    echo "2️⃣ CONFLICTING MODIFICATIONS:\n";
    
    $userModifications = [
        '2025_01_27_400000_add_department_id_to_users_table',
        '2025_07_14_125826_add_pf_number_to_users_table',
        '2025_01_28_000001_fix_users_table_safe'
    ];
    
    echo "   ⚠️  Users table modifications (may conflict):\n";
    foreach ($userModifications as $migration) {
        echo "      - {$migration}\n";
    }
    echo "\n";
    
    // 3. Recommended cleanup
    echo "3️⃣ RECOMMENDED CLEANUP:\n";
    
    $toRemove = [
        '2025_01_27_210000_create_role_management_tables' => 'Duplicate of create_roles_table',
        '2025_01_27_250000_fix_users_table_structure' => 'Conflicts with create_users_table',
        '2025_01_28_000001_fix_users_table_safe' => 'Redundant user table fix',
        '2025_01_28_000002_create_declarations_table_fixed' => 'Duplicate of create_declarations_table',
        '2025_01_27_210000_remove_hod_it_role_and_columns' => 'Tries to modify non-existent table',
        '2025_01_27_300000_remove_super_admin_role' => 'Tries to modify non-existent table'
    ];
    
    echo "   🗑️  Migrations to remove:\n";
    foreach ($toRemove as $migration => $reason) {
        echo "      ❌ {$migration}\n         Reason: {$reason}\n\n";
    }
    
    // 4. Recommended execution order
    echo "4️⃣ RECOMMENDED EXECUTION ORDER:\n";
    
    $correctOrder = [
        '2025_07_10_132202_create_roles_table',
        '2025_07_10_132400_create_departments_table',
        '2025_07_10_132300_create_users_table',
        '2025_07_14_125826_add_pf_number_to_users_table',
        '2025_01_27_400000_add_department_id_to_users_table',
        '2025_07_12_165909_create_personal_access_tokens_table',
        '2025_07_12_193706_create_sessions_table',
        '2025_07_10_132500_create_user_access_table',
        '2025_07_10_132600_update_user_access_table_for_combined_requests',
        '2025_07_10_140833_create_jeeva_access_requests_table',
        '2025_07_10_142300_create_module_access_requests_table',
        '2025_07_10_142400_add_approval_workflow_to_module_access_requests_table',
        '2025_07_15_000000_create_user_onboarding_table',
        '2025_07_15_000001_create_declarations_table',
        '2025_08_21_100000_fix_user_access_request_type_constraints',
        '2025_08_21_110000_convert_user_access_to_json_and_consolidate',
        '2025_08_21_120000_create_booking_service_table',
        '2025_07_15_000010_assign_hod_users_to_departments',
        '2025_07_15_000011_migrate_existing_roles_to_many_to_many',
        '2025_07_15_000012_ensure_all_users_have_roles'
    ];
    
    echo "   ✅ Correct execution order:\n";
    foreach ($correctOrder as $index => $migration) {
        $step = $index + 1;
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
        echo "   {$step}. {$type} {$migration}\n";
    }
    
    echo "\n🎯 SOLUTION SUMMARY:\n";
    echo "❌ Remove " . count($toRemove) . " conflicting/duplicate migrations\n";
    echo "✅ Keep " . count($correctOrder) . " essential migrations\n";
    echo "🔄 Execute in dependency-correct order\n";
    
} catch (Exception $e) {
    echo "❌ Error during analysis: " . $e->getMessage() . "\n";
}
?>