<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserAccess;
use App\Models\User;
use App\Models\Department;
use App\Services\StatusMigrationService;
use App\Services\UserAccessWorkflowService;
use App\Traits\HandlesStatusQueries;

class StatusMigrationTest extends TestCase
{
    use RefreshDatabase, WithFaker, HandlesStatusQueries;

    protected $migrationService;
    protected $workflowService;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->migrationService = new StatusMigrationService();
        $this->workflowService = new UserAccessWorkflowService();
    }

    /** @test */
    public function it_can_migrate_status_to_specific_columns()
    {
        // Create test data
        $userAccess = UserAccess::factory()->create(['status' => 'hod_approved']);
        
        // Test the migration
        $mapping = $this->migrationService->mapOldStatusToNewColumns('hod_approved');
        $userAccess->update($mapping);
        $userAccess->refresh();
        
        $this->assertEquals('approved', $userAccess->hod_status);
        $this->assertEquals('pending', $userAccess->divisional_status);
        $this->assertEquals('pending', $userAccess->ict_director_status);
        $this->assertEquals('pending', $userAccess->head_it_status);
        $this->assertEquals('pending', $userAccess->ict_officer_status);
    }

    /** @test */
    public function it_can_calculate_overall_status_from_columns()
    {
        $statusColumns = [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ];
        
        $overallStatus = $this->migrationService->calculateOverallStatus($statusColumns);
        $this->assertEquals('pending_ict_director', $overallStatus);
    }

    /** @test */
    public function it_can_detect_next_pending_stage()
    {
        $statusColumns = [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ];
        
        $nextStage = $this->migrationService->getNextPendingStage($statusColumns);
        $this->assertEquals('ict_director', $nextStage);
    }

    /** @test */
    public function it_can_detect_rejections_in_workflow()
    {
        $statusColumns = [
            'hod_status' => 'approved',
            'divisional_status' => 'rejected',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ];
        
        $hasRejections = $this->migrationService->hasRejections($statusColumns);
        $this->assertTrue($hasRejections);
        
        $statusColumns['divisional_status'] = 'approved';
        $hasRejections = $this->migrationService->hasRejections($statusColumns);
        $this->assertFalse($hasRejections);
    }

    /** @test */
    public function it_can_detect_completed_workflow()
    {
        $statusColumns = [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'implemented'
        ];
        
        $isComplete = $this->migrationService->isWorkflowComplete($statusColumns);
        $this->assertTrue($isComplete);
        
        $statusColumns['ict_officer_status'] = 'pending';
        $isComplete = $this->migrationService->isWorkflowComplete($statusColumns);
        $this->assertFalse($isComplete);
    }

    /** @test */
    public function it_can_calculate_workflow_progress()
    {
        $statusColumns = [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ];
        
        $progress = $this->migrationService->getWorkflowProgress($statusColumns);
        $this->assertEquals(60, $progress); // 3 out of 5 stages completed
    }

    /** @test */
    public function it_can_query_pending_requests_by_stage()
    {
        // Create test requests
        UserAccess::factory()->create([
            'hod_status' => 'pending',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        $hodPending = $this->getPendingRequestsForStage('hod')->count();
        $divisionalPending = $this->getPendingRequestsForStage('divisional')->count();
        $ictDirectorPending = $this->getPendingRequestsForStage('ict_director')->count();

        $this->assertEquals(1, $hodPending);
        $this->assertEquals(1, $divisionalPending);
        $this->assertEquals(1, $ictDirectorPending);
    }

    /** @test */
    public function it_can_query_approved_requests_by_stage()
    {
        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'implemented'
        ]);

        $hodApproved = $this->getApprovedRequestsForStage('hod')->count();
        $divisionalApproved = $this->getApprovedRequestsForStage('divisional')->count();
        $ictDirectorApproved = $this->getApprovedRequestsForStage('ict_director')->count();
        $headItApproved = $this->getApprovedRequestsForStage('head_it')->count();
        $ictOfficerApproved = $this->getApprovedRequestsForStage('ict_officer')->count();

        $this->assertEquals(1, $hodApproved);
        $this->assertEquals(1, $divisionalApproved);
        $this->assertEquals(1, $ictDirectorApproved);
        $this->assertEquals(1, $headItApproved);
        $this->assertEquals(1, $ictOfficerApproved);
    }

    /** @test */
    public function it_can_query_rejected_requests_by_stage()
    {
        UserAccess::factory()->create([
            'hod_status' => 'rejected',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'rejected',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        $hodRejected = $this->getRejectedRequestsForStage('hod')->count();
        $divisionalRejected = $this->getRejectedRequestsForStage('divisional')->count();

        $this->assertEquals(1, $hodRejected);
        $this->assertEquals(1, $divisionalRejected);
    }

    /** @test */
    public function it_can_get_system_statistics()
    {
        // Create various request states
        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'implemented'
        ]); // Completed

        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]); // In progress

        UserAccess::factory()->create([
            'hod_status' => 'rejected',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]); // Rejected

        $stats = $this->getSystemStatistics();

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['pending']); // In progress
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(1, $stats['rejected']);
    }

    /** @test */
    public function it_correctly_handles_all_status_mappings()
    {
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

        foreach ($testMappings as $oldStatus => $expected) {
            $mapping = $this->migrationService->mapOldStatusToNewColumns($oldStatus);
            
            foreach ($expected as $key => $value) {
                if ($key === 'overall') {
                    $calculatedStatus = $this->migrationService->calculateOverallStatus($mapping);
                    $this->assertEquals($value, $calculatedStatus, "Failed for status: {$oldStatus}, overall status");
                } else {
                    $this->assertEquals($value, $mapping[$key], "Failed for status: {$oldStatus}, column: {$key}");
                }
            }
        }
    }

    /** @test */
    public function it_can_identify_requests_ready_for_each_approval_stage()
    {
        // Request ready for divisional approval
        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'pending'
        ]);

        // Request ready for ICT director approval
        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending'
        ]);

        // Request ready for Head IT approval
        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'pending'
        ]);

        // Request ready for ICT officer implementation
        UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'pending'
        ]);

        $readyForDivisional = $this->getPendingRequestsForStage('divisional')->count();
        $readyForIctDirector = $this->getPendingRequestsForStage('ict_director')->count();
        $readyForHeadIt = $this->getPendingRequestsForStage('head_it')->count();
        $readyForIctOfficer = $this->getPendingRequestsForStage('ict_officer')->count();

        $this->assertEquals(1, $readyForDivisional);
        $this->assertEquals(1, $readyForIctDirector);
        $this->assertEquals(1, $readyForHeadIt);
        $this->assertEquals(1, $readyForIctOfficer);
    }

    /** @test */
    public function user_access_model_methods_work_correctly()
    {
        $userAccess = UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        $this->assertEquals('pending_head_it', $userAccess->getCalculatedOverallStatus());
        $this->assertEquals('head_it', $userAccess->getNextPendingStageFromColumns());
        $this->assertFalse($userAccess->isWorkflowCompleteByColumns());
        $this->assertFalse($userAccess->hasRejectionsInColumns());
        $this->assertEquals(60, $userAccess->getWorkflowProgressFromColumns());

        // Test with rejections
        $userAccess->update(['divisional_status' => 'rejected']);
        $this->assertTrue($userAccess->hasRejectionsInColumns());

        // Test with completed workflow
        $userAccess->update([
            'divisional_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'implemented'
        ]);
        $this->assertTrue($userAccess->isWorkflowCompleteByColumns());
        $this->assertEquals(100, $userAccess->getWorkflowProgressFromColumns());
    }

    /** @test */
    public function workflow_service_updates_new_status_columns()
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $userAccess = UserAccess::factory()->create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'status' => 'pending_hod',
            'hod_status' => 'pending'
        ]);

        // Mock request for HOD approval
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'action' => 'approve',
            'comments' => 'Test approval',
            'hod_name' => 'Test HOD'
        ]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->workflowService->processHodApproval($userAccess, $request);
        $userAccess->refresh();

        $this->assertEquals('approved', $userAccess->hod_status);
        $this->assertEquals('pending_divisional', $userAccess->status);
    }

    /** @test */
    public function it_handles_edge_cases_correctly()
    {
        // Test with null status columns
        $userAccess = UserAccess::factory()->create([
            'hod_status' => null,
            'divisional_status' => null,
            'ict_director_status' => null,
            'head_it_status' => null,
            'ict_officer_status' => null
        ]);

        $this->assertEquals('pending', $userAccess->getHodApprovalStatus());
        $this->assertEquals('pending', $userAccess->getDivisionalApprovalStatus());
        $this->assertEquals('pending', $userAccess->getIctDirectorApprovalStatus());
        $this->assertEquals('pending', $userAccess->getHeadItApprovalStatus());
        $this->assertEquals('pending', $userAccess->getIctOfficerImplementationStatus());

        // Test with invalid status values
        $mapping = $this->migrationService->mapOldStatusToNewColumns('invalid_status');
        $expectedDefaultMapping = $this->migrationService->mapOldStatusToNewColumns('pending');
        $this->assertEquals($expectedDefaultMapping, $mapping);
    }
}
