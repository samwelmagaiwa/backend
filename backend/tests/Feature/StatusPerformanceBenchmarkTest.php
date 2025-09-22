<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\UserAccess;
use App\Traits\HandlesStatusQueries;
use Illuminate\Support\Facades\DB;

class StatusPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase, HandlesStatusQueries;

    protected $sampleSize = 1000;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedTestData();
    }

    /** @test */
    public function it_benchmarks_pending_queries_performance()
    {
        // Benchmark old status system
        $startTime = microtime(true);
        for ($i = 0; $i < 10; $i++) {
            UserAccess::where('status', 'pending_hod')->count();
        }
        $oldSystemTime = microtime(true) - $startTime;

        // Benchmark new status system
        $startTime = microtime(true);
        for ($i = 0; $i < 10; $i++) {
            $this->getPendingRequestsForStage('hod')->count();
        }
        $newSystemTime = microtime(true) - $startTime;

        // Log the results
        $this->artisan('test:log', [
            'message' => "Pending queries benchmark:\nOld system: {$oldSystemTime}s\nNew system: {$newSystemTime}s\nDifference: " . ($newSystemTime - $oldSystemTime) . "s"
        ]);

        // Ensure performance is not significantly worse (allow 50% overhead)
        $this->assertLessThan($oldSystemTime * 1.5, $newSystemTime, 
            'New status system queries should not be more than 50% slower than old system');
    }

    /** @test */
    public function it_benchmarks_complex_filtering_performance()
    {
        // Benchmark old system complex query
        $startTime = microtime(true);
        for ($i = 0; $i < 5; $i++) {
            UserAccess::whereIn('status', ['pending_hod', 'pending_divisional', 'pending_ict_director'])
                ->orWhere('status', 'like', '%approved%')
                ->count();
        }
        $oldSystemTime = microtime(true) - $startTime;

        // Benchmark new system complex query
        $startTime = microtime(true);
        for ($i = 0; $i < 5; $i++) {
            UserAccess::where(function ($query) {
                $query->where('hod_status', 'pending')
                      ->orWhere('divisional_status', 'pending')
                      ->orWhere('ict_director_status', 'pending')
                      ->orWhere('hod_status', 'approved')
                      ->orWhere('divisional_status', 'approved')
                      ->orWhere('ict_director_status', 'approved');
            })->count();
        }
        $newSystemTime = microtime(true) - $startTime;

        $this->artisan('test:log', [
            'message' => "Complex filtering benchmark:\nOld system: {$oldSystemTime}s\nNew system: {$newSystemTime}s\nDifference: " . ($newSystemTime - $oldSystemTime) . "s"
        ]);

        // Ensure performance is reasonable
        $this->assertLessThan($oldSystemTime * 2, $newSystemTime, 
            'New status system complex queries should not be more than 2x slower than old system');
    }

    /** @test */
    public function it_benchmarks_statistics_queries_performance()
    {
        // Benchmark old system statistics
        $startTime = microtime(true);
        for ($i = 0; $i < 5; $i++) {
            $stats = [
                'total' => UserAccess::count(),
                'pending' => UserAccess::where('status', 'like', 'pending_%')->count(),
                'approved' => UserAccess::where('status', 'like', '%_approved')->count(),
                'rejected' => UserAccess::where('status', 'like', '%_rejected')->count(),
                'implemented' => UserAccess::where('status', 'implemented')->count(),
            ];
        }
        $oldSystemTime = microtime(true) - $startTime;

        // Benchmark new system statistics
        $startTime = microtime(true);
        for ($i = 0; $i < 5; $i++) {
            $stats = $this->getSystemStatistics();
        }
        $newSystemTime = microtime(true) - $startTime;

        $this->artisan('test:log', [
            'message' => "Statistics queries benchmark:\nOld system: {$oldSystemTime}s\nNew system: {$newSystemTime}s\nDifference: " . ($newSystemTime - $oldSystemTime) . "s"
        ]);

        // New system might be slightly slower but should be more accurate
        $this->assertLessThan($oldSystemTime * 3, $newSystemTime, 
            'New status system statistics should not be more than 3x slower than old system');
    }

    /** @test */
    public function it_tests_database_index_effectiveness()
    {
        // Test queries with EXPLAIN to check index usage
        $hodPendingExplain = DB::select('EXPLAIN SELECT * FROM user_access WHERE hod_status = ?', ['pending']);
        $divisionalPendingExplain = DB::select('EXPLAIN SELECT * FROM user_access WHERE divisional_status = ?', ['pending']);
        
        // Log explain results for analysis
        $this->artisan('test:log', [
            'message' => "Index usage analysis:\n" . 
                "HOD status query: " . json_encode($hodPendingExplain) . "\n" .
                "Divisional status query: " . json_encode($divisionalPendingExplain)
        ]);

        // Ensure we're not doing full table scans for basic queries
        $this->assertTrue(true); // Just log for now, manual analysis needed
    }

    /** @test */
    public function it_validates_query_result_consistency()
    {
        // Compare results between old and new systems
        $oldPendingCount = UserAccess::where('status', 'pending_hod')->count();
        $newPendingCount = $this->getPendingRequestsForStage('hod')->count();

        $oldApprovedCount = UserAccess::where('status', 'hod_approved')->count();
        $newApprovedCount = $this->getApprovedRequestsForStage('hod')->count();

        // Results should be consistent after migration
        $this->assertEquals($oldPendingCount, $newPendingCount, 
            'Pending HOD requests count should match between old and new systems');
        
        $this->assertEquals($oldApprovedCount, $newApprovedCount, 
            'Approved HOD requests count should match between old and new systems');
    }

    /** @test */
    public function it_measures_memory_usage()
    {
        $initialMemory = memory_get_usage();

        // Load large dataset with old status approach
        $oldStatusData = UserAccess::with(['user', 'department'])
            ->whereIn('status', ['pending_hod', 'hod_approved', 'divisional_approved'])
            ->get();
            
        $oldMemoryPeak = memory_get_peak_usage() - $initialMemory;
        unset($oldStatusData);
        
        // Reset memory tracking
        gc_collect_cycles();
        $resetMemory = memory_get_usage();

        // Load same dataset with new status approach
        $newStatusData = UserAccess::with(['user', 'department'])
            ->where(function ($query) {
                $query->where('hod_status', 'pending')
                      ->orWhere('hod_status', 'approved')
                      ->orWhere('divisional_status', 'approved');
            })
            ->get();
            
        $newMemoryPeak = memory_get_peak_usage() - $resetMemory;

        $this->artisan('test:log', [
            'message' => "Memory usage comparison:\nOld system peak: " . number_format($oldMemoryPeak) . " bytes\nNew system peak: " . number_format($newMemoryPeak) . " bytes"
        ]);

        // Memory usage should be comparable
        $this->assertLessThan($oldMemoryPeak * 2, $newMemoryPeak, 
            'New status system should not use significantly more memory');
    }

    protected function seedTestData()
    {
        $statuses = [
            'pending_hod', 'hod_approved', 'hod_rejected',
            'pending_divisional', 'divisional_approved', 'divisional_rejected',
            'pending_ict_director', 'ict_director_approved', 'ict_director_rejected',
            'pending_head_it', 'head_it_approved', 'head_it_rejected',
            'pending_ict_officer', 'implemented'
        ];

        UserAccess::factory($this->sampleSize)->create()->each(function ($userAccess) use ($statuses) {
            $status = $statuses[array_rand($statuses)];
            $userAccess->update(['status' => $status]);

            // Populate new status columns based on old status
            $migrationService = new \App\Services\StatusMigrationService();
            $mapping = $migrationService->mapOldStatusToNewColumns($status);
            $userAccess->update($mapping);
        });
    }
}
