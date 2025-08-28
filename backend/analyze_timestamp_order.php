<?php

echo "🕐 Migration Timestamp Order Analysis\n";
echo "====================================\n\n";

// List of migrations with their timestamps
$migrations = [
    '2025_07_10_132202_create_roles_table',
    '2025_07_10_132300_create_users_table',
    '2025_07_10_132350_add_department_id_to_users_table',
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

echo "📋 Current execution order (by timestamp):\n";
foreach ($migrations as $index => $migration) {
    $step = $index + 1;
    
    // Extract timestamp
    preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_(.+)$/', $migration, $matches);
    $timestamp = $matches[1] ?? 'unknown';
    $name = $matches[2] ?? $migration;
    
    // Determine type and potential issues
    $type = '';
    $issue = '';
    
    if (strpos($name, 'create_') === 0) {
        $type = '🏗️  [CREATE]';
    } elseif (strpos($name, 'add_') === 0 || strpos($name, 'update_') === 0) {
        $type = '🔧 [MODIFY]';
    } elseif (strpos($name, 'assign_') === 0 || strpos($name, 'migrate_') === 0 || strpos($name, 'ensure_') === 0) {
        $type = '📊 [DATA]';
    } else {
        $type = '⚙️  [OTHER]';
    }
    
    // Check for specific issues
    if ($name === 'add_department_id_to_users_table') {
        $issue = ' ❌ RUNS BEFORE departments table creation!';
    }
    
    echo "   {$step}. {$type} {$timestamp} - {$name}{$issue}\n";
}

echo "\n🚨 CRITICAL ISSUES FOUND:\n";

$issues = [
    [
        'issue' => 'add_department_id_to_users_table runs BEFORE create_departments_table',
        'current_order' => [
            '3. add_department_id_to_users_table (132350)',
            '4. create_departments_table (132400)'
        ],
        'problem' => 'Foreign key constraint will fail - departments table doesn\'t exist yet',
        'solution' => 'Move add_department_id_to_users_table to run AFTER create_departments_table'
    ]
];

foreach ($issues as $index => $issue) {
    $num = $index + 1;
    echo "\n{$num}. ❌ {$issue['issue']}\n";
    echo "   Current order:\n";
    foreach ($issue['current_order'] as $order) {
        echo "      {$order}\n";
    }
    echo "   Problem: {$issue['problem']}\n";
    echo "   Solution: {$issue['solution']}\n";
}

echo "\n✅ RECOMMENDED FIXES:\n";

echo "\n1. Move add_department_id_to_users_table to timestamp 2025_07_10_132450\n";
echo "   This will make it run AFTER departments table creation\n";

echo "\n📋 CORRECTED ORDER:\n";

$correctedOrder = [
    '2025_07_10_132202_create_roles_table',
    '2025_07_10_132300_create_users_table',
    '2025_07_10_132400_create_departments_table',
    '2025_07_10_132450_add_department_id_to_users_table', // MOVED HERE
    '2025_07_10_132500_create_user_access_table',
    '2025_07_10_132600_update_user_access_table_for_combined_requests',
    '2025_07_14_125826_add_pf_number_to_users_table',
    // ... rest of migrations
];

echo "\n   ✅ Correct execution order:\n";
foreach ($correctedOrder as $index => $migration) {
    $step = $index + 1;
    
    preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_(.+)$/', $migration, $matches);
    $timestamp = $matches[1] ?? 'unknown';
    $name = $matches[2] ?? $migration;
    
    $type = '';
    if (strpos($name, 'create_') === 0) {
        $type = '🏗️ ';
    } elseif (strpos($name, 'add_') === 0) {
        $type = '🔧';
    }
    
    $highlight = '';
    if ($name === 'add_department_id_to_users_table') {
        $highlight = ' ← FIXED POSITION';
    }
    
    echo "   {$step}. {$type} {$timestamp} - {$name}{$highlight}\n";
}

echo "\n🎯 SUMMARY:\n";
echo "❌ Current order: add_department_id runs before departments table exists\n";
echo "✅ Fixed order: departments table created first, then add_department_id\n";
echo "🔧 Action needed: Rename migration file to fix timestamp\n";

?>