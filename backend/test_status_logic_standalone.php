<?php

/**
 * Standalone Status Migration Logic Test
 * 
 * This tests the core status migration logic without Laravel dependencies
 */

class StatusMigrationLogicTest 
{
    private $testResults = [];

    public function runAllTests()
    {
        echo "🚀 Starting Status Migration Logic Tests\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $this->testStatusMappings();
        $this->testStatusCalculations();
        $this->testWorkflowProgress();
        $this->testEdgeCases();
        
        $this->printSummary();
    }

    private function testStatusMappings()
    {
        echo "📋 Testing Status Mappings\n";
        echo "-" . str_repeat("-", 30) . "\n";
        
        $testMappings = [
            'pending' => [
                'hod_status' => 'pending',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'hod_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'hod_rejected' => [
                'hod_status' => 'rejected',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'divisional_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'divisional_rejected' => [
                'hod_status' => 'approved',
                'divisional_status' => 'rejected',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'ict_director_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'ict_director_rejected' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'rejected',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'head_it_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'approved',
                'ict_officer_status' => 'pending'
            ],
            'head_it_rejected' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'rejected',
                'ict_officer_status' => 'pending'
            ],
            'implemented' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'approved',
                'ict_officer_status' => 'implemented'
            ]
        ];

        $passed = 0;
        $total = count($testMappings);

        foreach ($testMappings as $oldStatus => $expected) {
            $mapping = $this->mapOldStatusToNewColumns($oldStatus);
            
            $testPassed = true;
            foreach ($expected as $column => $expectedValue) {
                if (!isset($mapping[$column]) || $mapping[$column] !== $expectedValue) {
                    echo "❌ Failed: {$oldStatus} -> {$column} should be {$expectedValue}, got " . ($mapping[$column] ?? 'null') . "\n";
                    $testPassed = false;
                }
            }
            
            if ($testPassed) {
                echo "✅ Passed: {$oldStatus} mapping\n";
                $passed++;
            }
        }

        $this->testResults['mappings'] = ['passed' => $passed, 'total' => $total];
        echo "\nMapping Tests: {$passed}/{$total} passed\n\n";
    }

    private function testStatusCalculations()
    {
        echo "🔄 Testing Status Calculations\n";
        echo "-" . str_repeat("-", 30) . "\n";
        
        $testCases = [
            [
                'name' => 'Pending HOD',
                'columns' => [
                    'hod_status' => 'pending',
                    'divisional_status' => 'pending',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_overall' => 'pending_hod',
                'expected_next_stage' => 'hod'
            ],
            [
                'name' => 'Pending Divisional',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'pending',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_overall' => 'pending_divisional',
                'expected_next_stage' => 'divisional'
            ],
            [
                'name' => 'HOD Rejected',
                'columns' => [
                    'hod_status' => 'rejected',
                    'divisional_status' => 'pending',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_overall' => 'hod_rejected',
                'expected_next_stage' => 'hod'
            ],
            [
                'name' => 'Implemented',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'head_it_status' => 'approved',
                    'ict_officer_status' => 'implemented'
                ],
                'expected_overall' => 'implemented',
                'expected_next_stage' => null
            ]
        ];

        $passed = 0;
        $total = count($testCases);

        foreach ($testCases as $case) {
            $overallStatus = $this->calculateOverallStatus($case['columns']);
            $nextStage = $this->getNextPendingStage($case['columns']);
            
            $testPassed = true;
            
            if ($overallStatus !== $case['expected_overall']) {
                echo "❌ {$case['name']}: Expected overall={$case['expected_overall']}, got {$overallStatus}\n";
                $testPassed = false;
            }
            
            if ($nextStage !== $case['expected_next_stage']) {
                echo "❌ {$case['name']}: Expected next stage={$case['expected_next_stage']}, got {$nextStage}\n";
                $testPassed = false;
            }
            
            if ($testPassed) {
                echo "✅ Passed: {$case['name']}\n";
                $passed++;
            }
        }

        $this->testResults['calculations'] = ['passed' => $passed, 'total' => $total];
        echo "\nCalculation Tests: {$passed}/{$total} passed\n\n";
    }

    private function testWorkflowProgress()
    {
        echo "📊 Testing Workflow Progress\n";
        echo "-" . str_repeat("-", 30) . "\n";
        
        $testCases = [
            [
                'name' => '0% Progress',
                'columns' => [
                    'hod_status' => 'pending',
                    'divisional_status' => 'pending',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_progress' => 0
            ],
            [
                'name' => '20% Progress (HOD approved)',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'pending',
                    'ict_director_status' => 'pending',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_progress' => 20
            ],
            [
                'name' => '60% Progress',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'head_it_status' => 'pending',
                    'ict_officer_status' => 'pending'
                ],
                'expected_progress' => 60
            ],
            [
                'name' => '100% Progress',
                'columns' => [
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'head_it_status' => 'approved',
                    'ict_officer_status' => 'implemented'
                ],
                'expected_progress' => 100
            ]
        ];

        $passed = 0;
        $total = count($testCases);

        foreach ($testCases as $case) {
            $progress = $this->getWorkflowProgress($case['columns']);
            
            if ($progress == $case['expected_progress']) {
                echo "✅ Passed: {$case['name']} - {$progress}%\n";
                $passed++;
            } else {
                echo "❌ Failed: {$case['name']} - Expected {$case['expected_progress']}%, got {$progress}%\n";
            }
        }

        $this->testResults['progress'] = ['passed' => $passed, 'total' => $total];
        echo "\nProgress Tests: {$passed}/{$total} passed\n\n";
    }

    private function testEdgeCases()
    {
        echo "🧪 Testing Edge Cases\n";
        echo "-" . str_repeat("-", 30) . "\n";
        
        $passed = 0;
        $total = 0;

        // Test invalid status
        $total++;
        $invalidMapping = $this->mapOldStatusToNewColumns('invalid_status');
        $defaultMapping = $this->mapOldStatusToNewColumns('pending');
        
        if ($invalidMapping === $defaultMapping) {
            echo "✅ Invalid status defaults to pending\n";
            $passed++;
        } else {
            echo "❌ Invalid status doesn't default to pending\n";
        }

        // Test null values
        $total++;
        $nullColumns = [
            'hod_status' => null,
            'divisional_status' => null,
            'ict_director_status' => null,
            'head_it_status' => null,
            'ict_officer_status' => null
        ];
        
        $overallStatus = $this->calculateOverallStatus($nullColumns);
        
        if ($overallStatus === 'pending_hod') {
            echo "✅ Null values default to pending_hod\n";
            $passed++;
        } else {
            echo "❌ Null values don't default to pending_hod, got: {$overallStatus}\n";
        }

        // Test empty strings
        $total++;
        $emptyColumns = [
            'hod_status' => '',
            'divisional_status' => '',
            'ict_director_status' => '',
            'head_it_status' => '',
            'ict_officer_status' => ''
        ];
        
        $progress = $this->getWorkflowProgress($emptyColumns);
        
        if ($progress == 0) {
            echo "✅ Empty strings return 0% progress\n";
            $passed++;
        } else {
            echo "❌ Empty strings don't return 0% progress, got: {$progress}%\n";
        }

        $this->testResults['edge_cases'] = ['passed' => $passed, 'total' => $total];
        echo "\nEdge Cases Tests: {$passed}/{$total} passed\n\n";
    }

    private function printSummary()
    {
        echo "📋 TEST SUMMARY\n";
        echo "=" . str_repeat("=", 50) . "\n";
        
        $totalPassed = 0;
        $totalTests = 0;
        
        foreach ($this->testResults as $category => $result) {
            $totalPassed += $result['passed'];
            $totalTests += $result['total'];
            $percentage = $result['total'] > 0 ? round(($result['passed'] / $result['total']) * 100, 1) : 0;
            
            $status = $result['passed'] === $result['total'] ? "✅" : "⚠️";
            echo "{$status} " . ucwords($category) . ": {$result['passed']}/{$result['total']} ({$percentage}%)\n";
        }
        
        $overallPercentage = $totalTests > 0 ? round(($totalPassed / $totalTests) * 100, 1) : 0;
        $overallStatus = $totalPassed === $totalTests ? "🎉" : ($overallPercentage >= 80 ? "⚠️" : "❌");
        
        echo "\n{$overallStatus} OVERALL: {$totalPassed}/{$totalTests} tests passed ({$overallPercentage}%)\n";
        
        if ($overallPercentage === 100) {
            echo "\n🎉 All status migration logic tests passed!\n";
            echo "The core logic is working correctly and ready for integration.\n";
        } elseif ($overallPercentage >= 80) {
            echo "\n⚠️ Most tests passed, but some issues need attention.\n";
        } else {
            echo "\n❌ Significant logic issues found. Please review the implementation.\n";
        }
    }

    // Core logic methods (simplified versions of the actual service methods)
    
    private function mapOldStatusToNewColumns($oldStatus)
    {
        $mappings = [
            'pending' => [
                'hod_status' => 'pending',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'hod_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'hod_rejected' => [
                'hod_status' => 'rejected',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'divisional_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'divisional_rejected' => [
                'hod_status' => 'approved',
                'divisional_status' => 'rejected',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'ict_director_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'ict_director_rejected' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'rejected',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ],
            'head_it_approved' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'approved',
                'ict_officer_status' => 'pending'
            ],
            'head_it_rejected' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'rejected',
                'ict_officer_status' => 'pending'
            ],
            'implemented' => [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'approved',
                'ict_officer_status' => 'implemented'
            ]
        ];

        return $mappings[$oldStatus] ?? $mappings['pending'];
    }

    private function calculateOverallStatus($statusColumns)
    {
        // Normalize null/empty values to 'pending'
        foreach ($statusColumns as $key => $value) {
            if (empty($value)) {
                $statusColumns[$key] = 'pending';
            }
        }

        // Check for rejections first
        if ($statusColumns['hod_status'] === 'rejected') {
            return 'hod_rejected';
        }
        if ($statusColumns['divisional_status'] === 'rejected') {
            return 'divisional_rejected';
        }
        if ($statusColumns['ict_director_status'] === 'rejected') {
            return 'ict_director_rejected';
        }
        if ($statusColumns['head_it_status'] === 'rejected') {
            return 'head_it_rejected';
        }

        // Check completion
        if ($statusColumns['ict_officer_status'] === 'implemented') {
            return 'implemented';
        }

        // Find next pending stage
        $stages = [
            'hod' => 'hod_status',
            'divisional' => 'divisional_status',
            'ict_director' => 'ict_director_status',
            'head_it' => 'head_it_status',
            'ict_officer' => 'ict_officer_status'
        ];

        foreach ($stages as $stage => $column) {
            if ($statusColumns[$column] === 'pending') {
                return "pending_{$stage}";
            }
        }

        return 'pending_hod'; // Fallback
    }

    private function getNextPendingStage($statusColumns)
    {
        // Normalize null/empty values to 'pending'
        foreach ($statusColumns as $key => $value) {
            if (empty($value)) {
                $statusColumns[$key] = 'pending';
            }
        }

        $stages = [
            'hod' => 'hod_status',
            'divisional' => 'divisional_status',
            'ict_director' => 'ict_director_status',
            'head_it' => 'head_it_status',
            'ict_officer' => 'ict_officer_status'
        ];

        foreach ($stages as $stage => $column) {
            if ($statusColumns[$column] === 'pending' || $statusColumns[$column] === 'rejected') {
                return $stage;
            }
        }

        return null; // All completed
    }

    private function getWorkflowProgress($statusColumns)
    {
        // Normalize null/empty values to 'pending'
        foreach ($statusColumns as $key => $value) {
            if (empty($value)) {
                $statusColumns[$key] = 'pending';
            }
        }

        $stages = ['hod_status', 'divisional_status', 'ict_director_status', 'head_it_status', 'ict_officer_status'];
        $completed = 0;
        $total = count($stages);

        foreach ($stages as $stage) {
            if ($statusColumns[$stage] === 'approved' || $statusColumns[$stage] === 'implemented') {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }
}

// Run the test
echo "Status Migration Logic Test - Standalone Version\n";
echo "This tests the core logic without Laravel dependencies\n\n";

$test = new StatusMigrationLogicTest();
$test->runAllTests();
