<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\UserAccess;
use App\Services\StatusMigrationService;
use App\Services\UserAccessWorkflowService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== STATUS MIGRATION SYSTEM TEST ===" . PHP_EOL . PHP_EOL;

// Test 1: Check current data
echo "1. CHECKING CURRENT DATA:" . PHP_EOL;
$records = UserAccess::all();
echo "Total records: " . $records->count() . PHP_EOL;

foreach ($records as $record) {
    echo "Record ID {$record->id}:" . PHP_EOL;
    echo "  Legacy status: {$record->status}" . PHP_EOL;
    echo "  HOD status: " . ($record->hod_status ?? 'null') . PHP_EOL;
    echo "  Divisional status: " . ($record->divisional_status ?? 'null') . PHP_EOL;
    echo "  ICT Director status: " . ($record->ict_director_status ?? 'null') . PHP_EOL;
    echo "  Head IT status: " . ($record->head_it_status ?? 'null') . PHP_EOL;
    echo "  ICT Officer status: " . ($record->ict_officer_status ?? 'null') . PHP_EOL;
    echo PHP_EOL;
}

// Test 2: Test StatusMigrationService methods
echo "2. TESTING STATUS MIGRATION SERVICE:" . PHP_EOL;
$migrationService = new StatusMigrationService();

$testRecord = $records->first();
if ($testRecord) {
    echo "Testing with record ID {$testRecord->id} (status: {$testRecord->status}):" . PHP_EOL;
    
    $statusColumns = [
        'hod_status' => $testRecord->hod_status,
        'divisional_status' => $testRecord->divisional_status,
        'ict_director_status' => $testRecord->ict_director_status,
        'head_it_status' => $testRecord->head_it_status,
        'ict_officer_status' => $testRecord->ict_officer_status
    ];
    
    echo "  Calculated overall status: " . $migrationService->calculateOverallStatus($statusColumns) . PHP_EOL;
    echo "  Next pending stage: " . ($migrationService->getNextPendingStage($statusColumns) ?? 'none') . PHP_EOL;
    echo "  Workflow complete: " . ($migrationService->isWorkflowComplete($statusColumns) ? 'yes' : 'no') . PHP_EOL;
    echo "  Has rejections: " . ($migrationService->hasRejections($statusColumns) ? 'yes' : 'no') . PHP_EOL;
    echo "  Workflow progress: " . $migrationService->getWorkflowProgress($statusColumns) . "%" . PHP_EOL;
    echo PHP_EOL;
}

// Test 3: Test UserAccess model methods
echo "3. TESTING USERACCESS MODEL METHODS:" . PHP_EOL;
if ($testRecord) {
    echo "Using record ID {$testRecord->id}:" . PHP_EOL;
    echo "  getCalculatedOverallStatus(): " . $testRecord->getCalculatedOverallStatus() . PHP_EOL;
    echo "  getNextPendingStageFromColumns(): " . ($testRecord->getNextPendingStageFromColumns() ?? 'none') . PHP_EOL;
    echo "  isWorkflowCompleteByColumns(): " . ($testRecord->isWorkflowCompleteByColumns() ? 'yes' : 'no') . PHP_EOL;
    echo "  hasRejectionsInColumns(): " . ($testRecord->hasRejectionsInColumns() ? 'yes' : 'no') . PHP_EOL;
    echo "  getWorkflowProgressFromColumns(): " . $testRecord->getWorkflowProgressFromColumns() . "%" . PHP_EOL;
    echo PHP_EOL;
}

// Test 4: Test status mapping for different scenarios
echo "4. TESTING STATUS MAPPING FOR DIFFERENT SCENARIOS:" . PHP_EOL;
$testScenarios = [
    'pending',
    'pending_hod', 
    'hod_approved',
    'hod_rejected',
    'divisional_approved',
    'divisional_rejected',
    'ict_director_approved',
    'ict_director_rejected',
    'head_it_approved',
    'head_it_rejected',
    'implemented',
    'rejected'
];

foreach ($testScenarios as $scenario) {
    echo "Scenario: {$scenario}" . PHP_EOL;
    $mapping = $migrationService->mapOldStatusToNewColumns($scenario);
    echo "  HOD: {$mapping['hod_status']}" . PHP_EOL;
    echo "  Divisional: {$mapping['divisional_status']}" . PHP_EOL;
    echo "  ICT Director: {$mapping['ict_director_status']}" . PHP_EOL;
    echo "  Head IT: {$mapping['head_it_status']}" . PHP_EOL;
    echo "  ICT Officer: {$mapping['ict_officer_status']}" . PHP_EOL;
    echo "  Calculated overall: " . $migrationService->calculateOverallStatus($mapping) . PHP_EOL;
    echo PHP_EOL;
}

// Test 5: Verify database consistency
echo "5. VERIFYING DATABASE CONSISTENCY:" . PHP_EOL;
$inconsistencies = [];

foreach ($records as $record) {
    $expectedMapping = $migrationService->mapOldStatusToNewColumns($record->status, $record);
    $actualMapping = [
        'hod_status' => $record->hod_status,
        'divisional_status' => $record->divisional_status,
        'ict_director_status' => $record->ict_director_status,
        'head_it_status' => $record->head_it_status,
        'ict_officer_status' => $record->ict_officer_status
    ];
    
    $matches = true;
    foreach ($expectedMapping as $column => $expectedValue) {
        if ($actualMapping[$column] !== $expectedValue) {
            $matches = false;
            break;
        }
    }
    
    if (!$matches) {
        $inconsistencies[] = [
            'id' => $record->id,
            'status' => $record->status,
            'expected' => $expectedMapping,
            'actual' => $actualMapping
        ];
    }
}

if (empty($inconsistencies)) {
    echo "✓ All records are consistent with expected mappings" . PHP_EOL;
} else {
    echo "✗ Found " . count($inconsistencies) . " inconsistencies:" . PHP_EOL;
    foreach ($inconsistencies as $inconsistency) {
        echo "  Record ID {$inconsistency['id']} (status: {$inconsistency['status']}):" . PHP_EOL;
        echo "    Expected: " . json_encode($inconsistency['expected']) . PHP_EOL;
        echo "    Actual: " . json_encode($inconsistency['actual']) . PHP_EOL;
    }
}

echo PHP_EOL . "=== TEST COMPLETED ===" . PHP_EOL;
