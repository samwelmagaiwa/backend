<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\UserAccess;
use App\Models\User;
use App\Models\Department;
use App\Services\UserAccessWorkflowService;
use App\Http\Controllers\ICTApprovalController;
use App\Http\Controllers\HodDivisionalRecommendationsController;
use App\Http\Controllers\AccessRightsApprovalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $workflowService;
    protected $user;
    protected $department;
    protected $userAccess;

    public function setUp(): void
    {
        parent::setUp();
        $this->workflowService = new UserAccessWorkflowService();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->department = Department::factory()->create();
        $this->userAccess = UserAccess::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id,
            'status' => 'pending',
            'hod_status' => 'pending'
        ]);
    }

    /** @test */
    public function it_can_process_complete_approval_workflow()
    {
        // Step 1: HOD Approval
        $hodRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => 'HOD approval comments',
            'hod_name' => 'Test HOD'
        ]);

        $this->workflowService->processHodApproval($this->userAccess, $hodRequest);
        $this->userAccess->refresh();

        $this->assertEquals('approved', $this->userAccess->hod_status);
        $this->assertEquals('pending_divisional', $this->userAccess->status);
        $this->assertEquals('pending', $this->userAccess->divisional_status);

        // Step 2: Divisional Approval
        $divisionalRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => 'Divisional approval comments',
            'divisional_head_name' => 'Test Divisional Head'
        ]);

        $this->workflowService->processDivisionalApproval($this->userAccess, $divisionalRequest);
        $this->userAccess->refresh();

        $this->assertEquals('approved', $this->userAccess->divisional_status);
        $this->assertEquals('pending_ict_director', $this->userAccess->status);
        $this->assertEquals('pending', $this->userAccess->ict_director_status);

        // Step 3: ICT Director Approval
        $ictDirectorRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => 'ICT Director approval comments',
            'ict_director_name' => 'Test ICT Director'
        ]);

        $this->workflowService->processIctDirectorApproval($this->userAccess, $ictDirectorRequest);
        $this->userAccess->refresh();

        $this->assertEquals('approved', $this->userAccess->ict_director_status);
        $this->assertEquals('pending_head_it', $this->userAccess->status);
        $this->assertEquals('pending', $this->userAccess->head_it_status);

        // Step 4: Head IT Approval
        $headItRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => 'Head IT approval comments',
            'head_it_name' => 'Test Head IT'
        ]);

        $this->workflowService->processHeadItApproval($this->userAccess, $headItRequest);
        $this->userAccess->refresh();

        $this->assertEquals('approved', $this->userAccess->head_it_status);
        $this->assertEquals('pending_ict_officer', $this->userAccess->status);
        $this->assertEquals('pending', $this->userAccess->ict_officer_status);

        // Step 5: ICT Officer Implementation
        $ictOfficerRequest = $this->createRequest([
            'action' => 'implement',
            'comments' => 'Implementation completed',
            'ict_officer_name' => 'Test ICT Officer'
        ]);

        $this->workflowService->processIctOfficerImplementation($this->userAccess, $ictOfficerRequest);
        $this->userAccess->refresh();

        $this->assertEquals('implemented', $this->userAccess->ict_officer_status);
        $this->assertEquals('implemented', $this->userAccess->status);

        // Verify workflow is complete
        $this->assertTrue($this->userAccess->isWorkflowCompleteByColumns());
        $this->assertEquals(100, $this->userAccess->getWorkflowProgressFromColumns());
    }

    /** @test */
    public function it_handles_rejection_at_each_stage()
    {
        $rejectionScenarios = [
            ['stage' => 'hod', 'method' => 'processHodApproval'],
            ['stage' => 'divisional', 'method' => 'processDivisionalApproval'],
            ['stage' => 'ict_director', 'method' => 'processIctDirectorApproval'],
            ['stage' => 'head_it', 'method' => 'processHeadItApproval']
        ];

        foreach ($rejectionScenarios as $scenario) {
            // Create fresh test data for each scenario
            $userAccess = UserAccess::factory()->create([
                'user_id' => $this->user->id,
                'department_id' => $this->department->id,
                'status' => 'pending',
                'hod_status' => $scenario['stage'] === 'hod' ? 'pending' : 'approved',
                'divisional_status' => in_array($scenario['stage'], ['hod', 'divisional']) ? 'pending' : 'approved',
                'ict_director_status' => in_array($scenario['stage'], ['hod', 'divisional', 'ict_director']) ? 'pending' : 'approved',
                'head_it_status' => 'pending'
            ]);

            // Update status to reflect current stage
            if ($scenario['stage'] === 'divisional') {
                $userAccess->update(['status' => 'pending_divisional']);
            } elseif ($scenario['stage'] === 'ict_director') {
                $userAccess->update(['status' => 'pending_ict_director']);
            } elseif ($scenario['stage'] === 'head_it') {
                $userAccess->update(['status' => 'pending_head_it']);
            }

            $rejectionRequest = $this->createRequest([
                'action' => 'reject',
                'comments' => "Rejection at {$scenario['stage']} stage",
                $scenario['stage'] . '_name' => "Test {$scenario['stage']}"
            ]);

            $this->workflowService->{$scenario['method']}($userAccess, $rejectionRequest);
            $userAccess->refresh();

            // Verify rejection
            $statusColumn = $scenario['stage'] . '_status';
            $this->assertEquals('rejected', $userAccess->$statusColumn);
            $this->assertTrue($userAccess->hasRejectionsInColumns());
            $this->assertEquals($scenario['stage'] . '_rejected', $userAccess->status);
        }
    }

    /** @test */
    public function it_tests_controller_integration_with_new_status_system()
    {
        // Test ICTApprovalController
        $ictController = new ICTApprovalController();
        
        // Mock authentication
        Auth::shouldReceive('user')->andReturn($this->user);

        $hodApprovalRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => 'Controller test approval'
        ]);

        $response = $ictController->processHodApproval($hodApprovalRequest, $this->userAccess->id);
        $this->assertEquals(200, $response->getStatusCode());

        $this->userAccess->refresh();
        $this->assertEquals('approved', $this->userAccess->hod_status);
        $this->assertEquals('pending_divisional', $this->userAccess->status);
    }

    /** @test */
    public function it_tests_status_filtering_across_controllers()
    {
        // Create requests at different stages
        $pendingHod = UserAccess::factory()->create([
            'hod_status' => 'pending',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        $pendingDivisional = UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        $pendingIctDirector = UserAccess::factory()->create([
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        // Test HOD filtering
        $hodController = new HodDivisionalRecommendationsController();
        $hodRequest = new Request();
        $hodResponse = $hodController->getPendingRequests($hodRequest);
        
        $hodData = json_decode($hodResponse->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($hodData['data']));

        // Test ICT Director filtering
        $accessRightsController = new AccessRightsApprovalController();
        $directorRequest = new Request();
        $directorResponse = $accessRightsController->getPendingIctDirectorRequests($directorRequest);
        
        $directorData = json_decode($directorResponse->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($directorData['data']));
    }

    /** @test */
    public function it_handles_concurrent_workflow_processing()
    {
        // Create multiple requests that could be processed concurrently
        $requests = UserAccess::factory(5)->create([
            'hod_status' => 'pending',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        // Process all requests simultaneously (simulate concurrent processing)
        foreach ($requests as $request) {
            $approvalRequest = $this->createRequest([
                'action' => 'approve',
                'comments' => 'Concurrent processing test',
                'hod_name' => 'Test HOD'
            ]);

            $this->workflowService->processHodApproval($request, $approvalRequest);
        }

        // Verify all were processed correctly
        foreach ($requests as $request) {
            $request->refresh();
            $this->assertEquals('approved', $request->hod_status);
            $this->assertEquals('pending_divisional', $request->status);
        }
    }

    /** @test */
    public function it_validates_workflow_state_transitions()
    {
        // Test invalid state transitions
        $this->userAccess->update([
            'hod_status' => 'pending',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]);

        // Try to skip to divisional approval without HOD approval
        $divisionalRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => 'Invalid transition test'
        ]);

        try {
            $this->workflowService->processDivisionalApproval($this->userAccess, $divisionalRequest);
            $this->fail('Should have thrown exception for invalid workflow transition');
        } catch (\Exception $e) {
            $this->assertStringContainsString('HOD approval required', $e->getMessage());
        }
    }

    /** @test */
    public function it_maintains_audit_trail_throughout_workflow()
    {
        $this->actingAs($this->user);

        // Process complete workflow and verify audit entries
        $stages = [
            ['method' => 'processHodApproval', 'comments' => 'HOD audit test'],
            ['method' => 'processDivisionalApproval', 'comments' => 'Divisional audit test'],
            ['method' => 'processIctDirectorApproval', 'comments' => 'ICT Director audit test'],
            ['method' => 'processHeadItApproval', 'comments' => 'Head IT audit test'],
            ['method' => 'processIctOfficerImplementation', 'comments' => 'Implementation audit test']
        ];

        foreach ($stages as $stage) {
            $request = $this->createRequest([
                'action' => $stage['method'] === 'processIctOfficerImplementation' ? 'implement' : 'approve',
                'comments' => $stage['comments']
            ]);

            $this->workflowService->{$stage['method']}($this->userAccess, $request);
            $this->userAccess->refresh();

            // Verify comment was stored
            $this->assertStringContainsString($stage['comments'], $this->userAccess->comments ?? '');
        }
    }

    /** @test */
    public function it_handles_edge_cases_in_workflow()
    {
        // Test with null or empty values
        $edgeRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => '',
            'hod_name' => null
        ]);

        $this->workflowService->processHodApproval($this->userAccess, $edgeRequest);
        $this->userAccess->refresh();

        $this->assertEquals('approved', $this->userAccess->hod_status);

        // Test with very long comments
        $longCommentRequest = $this->createRequest([
            'action' => 'approve',
            'comments' => str_repeat('Long comment ', 100),
            'divisional_head_name' => 'Test Head'
        ]);

        $this->workflowService->processDivisionalApproval($this->userAccess, $longCommentRequest);
        $this->userAccess->refresh();

        $this->assertEquals('approved', $this->userAccess->divisional_status);
    }

    /** @test */
    public function it_preserves_data_integrity_during_workflow()
    {
        $originalData = [
            'name' => $this->userAccess->name,
            'email' => $this->userAccess->email,
            'user_id' => $this->userAccess->user_id,
            'department_id' => $this->userAccess->department_id,
            'created_at' => $this->userAccess->created_at
        ];

        // Process through complete workflow
        $this->processCompleteWorkflow();

        $this->userAccess->refresh();

        // Verify original data is preserved
        foreach ($originalData as $field => $value) {
            $this->assertEquals($value, $this->userAccess->$field, "Field {$field} should be preserved");
        }

        // Verify only status fields were updated
        $this->assertEquals('approved', $this->userAccess->hod_status);
        $this->assertEquals('approved', $this->userAccess->divisional_status);
        $this->assertEquals('approved', $this->userAccess->ict_director_status);
        $this->assertEquals('approved', $this->userAccess->head_it_status);
        $this->assertEquals('implemented', $this->userAccess->ict_officer_status);
    }

    protected function createRequest(array $data)
    {
        $request = new Request();
        $request->merge($data);
        $request->setUserResolver(function () {
            return $this->user;
        });
        return $request;
    }

    protected function processCompleteWorkflow()
    {
        $stages = [
            'processHodApproval',
            'processDivisionalApproval',
            'processIctDirectorApproval',
            'processHeadItApproval',
            'processIctOfficerImplementation'
        ];

        foreach ($stages as $stage) {
            $action = $stage === 'processIctOfficerImplementation' ? 'implement' : 'approve';
            $request = $this->createRequest([
                'action' => $action,
                'comments' => "Test {$stage}"
            ]);

            $this->workflowService->$stage($this->userAccess, $request);
            $this->userAccess->refresh();
        }
    }
}
