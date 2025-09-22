<?php

require_once 'bootstrap/app.php';

use App\Models\UserAccess;
use App\Services\StatusMigrationService;
use App\Services\UserAccessWorkflowService;
use App\Traits\HandlesStatusQueries;
use Illuminate\Support\Facades\DB;

class StatusMigrationComprehensiveTest
{
    use HandlesStatusQueries;
    
    private $migrationService;
    private $workflowService;
    private $testResults = [];
    
    public function __construct()
    {
        $this->migrationService = new StatusMigrationService();
        $this->workflowService = new UserAccessWorkflowService();
    }
    
    public function runAllTests()
    {
        echo "🚀 Starting Comprehensive Status Migration System Tests\n";
        echo "=" . str_repeat("=", 60) . "\n\n";
        
        $this->testStatusMigrationLogic();
        $this->testWorkflowProgression();
        $this->testStatusQueries();
        $this->testSystemStatistics();
        $this->testEdgeCases();
        $this->testPerformance();
        
        $this->printSummary();
    }
    
    private function testStatusMigrationLogic()
    {
        echo "📋 Testing Status Migration Logic\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        $testMappings = [
            'pending' => ['hod_status' => 'pending', 'overall' => 'pending_hod'],
            'hod_approved' => ['hod_status' => 'approved', 'overall' => 'pending_divisional'],
            'hod_rejected' => ['hod_status' => 'rejected', 'overall' => 'hod_rejected'],
            'divisional_approved' => ['divisional_status' => 'approved', 'overall' => 'pending_ict_director'],
            'divisional_rejected' => ['divisional_status' => 'rejected', 'overall' => 'divisional_rejected'],
            'ict_director_approved' => ['ict_director_status' => 'approved', 'overall' => 'pending_head_it'],
            'ict_director_rejected' => ['ict_director_status' => 'rejected', 'overall' => 'ict_director_rejected'],
            'head_it_approved' => ['head_it_status' => 'approved', 'overall' => 'pending_ict_officer'],
            'head_it_rejected' => ['head_it_status' => 'rejected', 'overall' => 'head_it_rejected'],
            'implemented' => ['ict_officer_status' => 'implemented', 'overall' => 'implemented']
        ];
        
        $passed = 0;
        $total = count($testMappings);
        
        foreach ($testMappings as $oldStatus => $expected) {
            try {
                $mapping = $this->migrationService->mapOldStatusToNewColumns($oldStatus);
                $overallStatus = $this->migrationService->calculateOverallStatus($mapping);
                
                $testPassed = true;
                foreach ($expected as $key => $value) {
                    if ($key === 'overall') {
                        if ($overallStatus !== $value) {
                            $testPassed = false;
                            echo "❌ Failed: {$oldStatus} -> overall should be {$value}, got {$overallStatus}\n";
                        }
                    } else {
                        if (!isset($mapping[$key]) || $mapping[$key] !== $value) {
                            $testPassed = false;
                            echo "❌ Failed: {$oldStatus} -> {$key} should be {$value}, got " . ($mapping[$key] ?? 'null') . "\n";
                        }
                    }
                }
                
                if ($testPassed) {
                    echo "✅ Passed: {$oldStatus} mapping\n";
                    $passed++;
                }
                
            } catch (Exception $e) {
                echo "❌ Error testing {$oldStatus}: " . $e->getMessage() . "\n";
            }
        }
        
        $this->testResults['migration_logic'] = ['passed' => $passed, 'total' => $total];
        echo "\nMigration Logic Tests: {$passed}/{$total} passed\n\n";
    }
    
    private function testWorkflowProgression()
    {
        echo "🔄 Testing Workflow Progression\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        $testCases = [
            [
                'name' => 'Complete Workflow',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'head_it_status' => 'approved',
                    'ict_officer_status' => 'implemented'
                ],
                'expected_complete' => true,
                'expected_progress' => 100,
                'expected_next_stage' => null
            ],
            [
                'name' => 'Halfway Progress',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_complete' => false,
                'expected_progress' => 40,
                'expected_next_stage' => 'ict_director'
            ],
            [
                'name' => 'With Rejections',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'rejected',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_complete' => false,
                'expected_progress' => 20,
                'expected_next_stage' => 'divisional',
                'has_rejections' => true
            ]
        ];
        
        $passed = 0;
        $total = count($testCases);
        
        foreach ($testCases as $case) {
            try {
                $isComplete = $this->migrationService->isWorkflowComplete($case['columns']);
                $progress = $this->migrationService->getWorkflowProgress($case['columns']);
                $nextStage = $this->migrationService->getNextPendingStage($case['columns']);
                $hasRejections = $this->migrationService->hasRejections($case['columns']);
                
                $testPassed = true;
                if ($isComplete !== $case['expected_complete']) {
                    echo "❌ {$case['name']}: Expected complete={$case['expected_complete']}, got {$isComplete}\n";
                    $testPassed = false;
                }
                
                if ($progress !== $case['expected_progress']) {
                    echo "❌ {$case['name']}: Expected progress={$case['expected_progress']}, got {$progress}\n";
                    $testPassed = false;
                }
                
                if ($nextStage !== $case['expected_next_stage']) {
                    echo "❌ {$case['name']}: Expected next stage={$case['expected_next_stage']}, got {$nextStage}\n";
                    $testPassed = false;
                }
                
                if (isset($case['has_rejections']) && $hasRejections !== $case['has_rejections']) {
                    echo "❌ {$case['name']}: Expected has rejections={$case['has_rejections']}, got {$hasRejections}\n";
                    $testPassed = false;
                }
                
                if ($testPassed) {
                    echo "✅ Passed: {$case['name']}\n";
                    $passed++;
                }
                
            } catch (Exception $e) {
                echo "❌ Error testing {$case['name']}: " . $e->getMessage() . "\n";
            }
        }
        
        $this->testResults['workflow_progression'] = ['passed' => $passed, 'total' => $total];
        echo "\nWorkflow Progression Tests: {$passed}/{$total} passed\n\n";
    }
    
    private function testStatusQueries()
    {
        echo "🔍 Testing Status Queries\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        $passed = 0;
        $total = 0;
        
        try {
            // Test pending queries for each stage
            $stages = ['hod', 'divisional', 'ict_director', 'head_it', 'ict_officer'];
            
            foreach ($stages as $stage) {
                $total++;
                try {
                    $pendingCount = $this->getPendingRequestsForStage($stage)->count();
                    $approvedCount = $this->getApprovedRequestsForStage($stage)->count();
                    $rejectedCount = $this->getRejectedRequestsForStage($stage)->count();
                    
                    echo "✅ {$stage} queries: pending={$pendingCount}, approved={$approvedCount}, rejected={$rejectedCount}\n";
                    $passed++;
                } catch (Exception $e) {
                    echo "❌ Error querying {$stage}: " . $e->getMessage() . "\n";
                }
            }
            
            // Test system statistics
            $total++;
            try {
                $stats = $this->getSystemStatistics();
                echo "✅ System stats: total={$stats['total']}, pending={$stats['pending']}, completed={$stats['completed']}, rejected={$stats['rejected']}\n";
                $passed++;
            } catch (Exception $e) {
                echo "❌ Error getting system statistics: " . $e->getMessage() . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Error in status queries test: " . $e->getMessage() . "\n";
        }
        
        $this->testResults['status_queries'] = ['passed' => $passed, 'total' => $total];
        echo "\nStatus Queries Tests: {$passed}/{$total} passed\n\n";
    }
    
    private function testSystemStatistics()
    {
        echo "📊 Testing System Statistics\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        $passed = 0;
        $total = 1;
        
        try {
            $stats = $this->getSystemStatistics();
            
            // Validate statistics structure
            $requiredKeys = ['total', 'pending', 'completed', 'rejected'];
            $hasAllKeys = true;
            
            foreach ($requiredKeys as $key) {
                if (!isset($stats[$key])) {
                    echo "❌ Missing key in statistics: {$key}\n";
                    $hasAllKeys = false;
                }
            }
            
            if ($hasAllKeys && is_numeric($stats['total']) && $stats['total'] >= 0) {
                echo "✅ System statistics structure is valid\n";
                echo "   Total: {$stats['total']}\n";
                echo "   Pending: {$stats['pending']}\n";
                echo "   Completed: {$stats['completed']}\n";
                echo "   Rejected: {$stats['rejected']}\n";
                $passed++;
            } else {
                echo "❌ Invalid system statistics structure\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Error testing system statistics: " . $e->getMessage() . "\n";
        }
        
        $this->testResults['system_statistics'] = ['passed' => $passed, 'total' => $total];
        echo "\nSystem Statistics Tests: {$passed}/{$total} passed\n\n";
    }
    
    private function testEdgeCases()
    {
        echo "🧪 Testing Edge Cases\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        $passed = 0;
        $total = 0;
        
        // Test invalid status mapping
        $total++;
        try {
            $mapping = $this->migrationService->mapOldStatusToNewColumns('invalid_status');
            $defaultMapping = $this->migrationService->mapOldStatusToNewColumns('pending');
            
            if ($mapping === $defaultMapping) {
                echo "✅ Invalid status defaults to pending mapping\n";
                $passed++;
            } else {
                echo "❌ Invalid status doesn't default to pending mapping\n";
            }
        } catch (Exception $e) {
            echo "❌ Error testing invalid status: " . $e->getMessage() . "\n";
        }
        
        // Test null status columns
        $total++;
        try {
            $statusColumns = [
                'hod_status' => null,
                'divisional_status' => null,
                'ict_director_status' => null,
                'head_it_status' => null,
                'ict_officer_status' => null
            ];
            
            $overallStatus = $this->migrationService->calculateOverallStatus($statusColumns);
            
            if ($overallStatus === 'pending_hod') {
                echo "✅ Null status columns default to pending_hod\n";
                $passed++;
            } else {
                echo "❌ Null status columns don't default to pending_hod, got: {$overallStatus}\n";
            }
        } catch (Exception $e) {
            echo "❌ Error testing null status columns: " . $e->getMessage() . "\n";
        }
        
        // Test empty status values
        $total++;
        try {
            $statusColumns = [
                'hod_status' => '',
                'divisional_status' => '',
                'ict_director_status' => '',
                'head_it_status' => '',
                'ict_officer_status' => ''
            ];
            
            $progress = $this->migrationService->getWorkflowProgress($statusColumns);
            
            if ($progress === 0) {
                echo "✅ Empty status columns return 0% progress\n";
                $passed++;
            } else {
                echo "❌ Empty status columns don't return 0% progress, got: {$progress}%\n";
            }
        } catch (Exception $e) {
            echo "❌ Error testing empty status values: " . $e->getMessage() . "\n";
        }
        
        $this->testResults['edge_cases'] = ['passed' => $passed, 'total' => $total];
        echo "\nEdge Cases Tests: {$passed}/{$total} passed\n\n";
    }
    
    private function testPerformance()
    {
        echo "⚡ Testing Performance\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        $passed = 0;
        $total = 0;
        
        // Test migration service performance
        $total++;
        try {
            $startTime = microtime(true);
            
            // Run mapping operations multiple times
            for ($i = 0; $i < 1000; $i++) {
                $this->migrationService->mapOldStatusToNewColumns('hod_approved');
                $this->migrationService->calculateOverallStatus([
                    'hod_status' => 'approved',
                    'divisional_status' => 'pending',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ]);
            }
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            if ($duration < 1.0) { // Should complete within 1 second
                echo "✅ Migration service performance: {$duration}s for 1000 operations\n";
                $passed++;
            } else {
                echo "❌ Migration service too slow: {$duration}s for 1000 operations\n";
            }
        } catch (Exception $e) {
            echo "❌ Error testing migration service performance: " . $e->getMessage() . "\n";
        }
        
        // Test query performance
        $total++;
        try {
            $startTime = microtime(true);
            
            // Run queries multiple times
            for ($i = 0; $i < 10; $i++) {
                $this->getSystemStatistics();
            }
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            if ($duration < 5.0) { // Should complete within 5 seconds
                echo "✅ Query performance: {$duration}s for 10 statistics queries\n";
                $passed++;
            } else {
                echo "❌ Queries too slow: {$duration}s for 10 statistics queries\n";
            }
        } catch (Exception $e) {
            echo "❌ Error testing query performance: " . $e->getMessage() . "\n";
        }
        
        $this->testResults['performance'] = ['passed' => $passed, 'total' => $total];
        echo "\nPerformance Tests: {$passed}/{$total} passed\n\n";
    }
    
    private function printSummary()
    {
        echo "📋 TEST SUMMARY\n";
        echo "=" . str_repeat("=", 60) . "\n";
        
        $totalPassed = 0;
        $totalTests = 0;
        
        foreach ($this->testResults as $category => $result) {
            $totalPassed += $result['passed'];
            $totalTests += $result['total'];
            $percentage = $result['total'] > 0 ? round(($result['passed'] / $result['total']) * 100, 1) : 0;
            
            $status = $result['passed'] === $result['total'] ? "✅" : "⚠️";
            echo "{$status} " . ucwords(str_replace('_', ' ', $category)) . ": {$result['passed']}/{$result['total']} ({$percentage}%)\n";
        }
        
        $overallPercentage = $totalTests > 0 ? round(($totalPassed / $totalTests) * 100, 1) : 0;
        $overallStatus = $totalPassed === $totalTests ? "🎉" : ($overallPercentage >= 80 ? "⚠️" : "❌");
        
        echo "\n{$overallStatus} OVERALL: {$totalPassed}/{$totalTests} tests passed ({$overallPercentage}%)\n";
        
        if ($overallPercentage === 100) {
            echo "\n🎉 Congratulations! All tests passed! The status migration system is working perfectly.\n";
        } elseif ($overallPercentage >= 80) {
            echo "\n⚠️ Most tests passed, but there are some issues to address.\n";
        } else {
            echo "\n❌ Significant issues found. Please review and fix the failing tests.\n";
        }
        
        echo "\n📝 Next steps:\n";
        echo "1. Address any failing tests\n";
        echo "2. Run performance benchmarks on production-like data\n";
        echo "3. Update remaining controllers to use new status system\n";
        echo "4. Update frontend components to use specific status columns\n";
        echo "5. Plan final migration to drop old status column\n";
    }
}

// Run the comprehensive test
try {
    $tester = new StatusMigrationComprehensiveTest();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "❌ Fatal error running tests: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
