<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BothServiceFormDivisionalDirectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'divisional_director']);
        Role::create(['name' => 'head_of_department']);
    }

    /**
     * Test divisional director approval endpoints exist and require authentication
     */
    public function test_divisional_director_endpoints_exist(): void
    {
        // Test approve endpoint
        $response = $this->postJson('/api/both-service-form/module-requests/1/divisional-approve');
        $response->assertStatus(401); // Should require authentication
        
        // Test reject endpoint
        $response = $this->postJson('/api/both-service-form/module-requests/1/divisional-reject');
        $response->assertStatus(401); // Should require authentication
    }

    /**
     * Test divisional director can approve requests
     */
    public function test_divisional_director_can_approve_request(): void
    {
        // Create test data
        $department = Department::create([
            'name' => 'Test Department',
            'code' => 'TEST'
        ]);
        
        $divisionalDirector = User::factory()->create([
            'name' => 'Test Divisional Director',
            'email' => 'dd@example.com',
            'department_id' => $department->id
        ]);
        $divisionalDirector->assignRole('divisional_director');
        
        $userAccess = UserAccess::create([
            'user_id' => User::factory()->create()->id,
            'pf_number' => 'TEST001',
            'staff_name' => 'Test Staff',
            'phone_number' => '1234567890',
            'department_id' => $department->id,
            'status' => 'hod_approved',
            'request_type' => ['wellsoft'],
            'wellsoft_modules_selected' => ['Registrar'],
            'jeeva_modules_selected' => ['FINANCIAL ACCOUNTING']
        ]);
        
        // Mock file storage
        Storage::fake('public');
        $signatureFile = UploadedFile::fake()->image('signature.png');
        
        // Make authenticated request
        $response = $this->actingAs($divisionalDirector, 'sanctum')
            ->postJson("/api/both-service-form/module-requests/{$userAccess->id}/divisional-approve", [
                'divisional_director_name' => 'Test Divisional Director',
                'approved_date' => '2024-01-15',
                'comments' => 'Approved for testing',
                'divisional_director_signature' => $signatureFile
            ]);
        
        // Verify response is successful
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Divisional Director approval updated successfully.'
            ]);
        
        // Verify database was updated
        $userAccess->refresh();
        $this->assertEquals('divisional_approved', $userAccess->status);
        $this->assertEquals('Test Divisional Director', $userAccess->divisional_director_name);
        $this->assertNotNull($userAccess->divisional_approved_at);
        $this->assertEquals('Approved for testing', $userAccess->divisional_director_comments);
        $this->assertNotNull($userAccess->divisional_director_signature_path);
    }

    /**
     * Test divisional director can reject requests
     */
    public function test_divisional_director_can_reject_request(): void
    {
        // Create test data
        $department = Department::create([
            'name' => 'Test Department',
            'code' => 'TEST'
        ]);
        
        $divisionalDirector = User::factory()->create([
            'name' => 'Test Divisional Director',
            'email' => 'dd@example.com',
            'department_id' => $department->id
        ]);
        $divisionalDirector->assignRole('divisional_director');
        
        $userAccess = UserAccess::create([
            'user_id' => User::factory()->create()->id,
            'pf_number' => 'TEST001',
            'staff_name' => 'Test Staff',
            'phone_number' => '1234567890',
            'department_id' => $department->id,
            'status' => 'hod_approved',
            'request_type' => ['wellsoft'],
            'wellsoft_modules_selected' => ['Registrar'],
            'jeeva_modules_selected' => ['FINANCIAL ACCOUNTING']
        ]);
        
        // Make authenticated request
        $response = $this->actingAs($divisionalDirector, 'sanctum')
            ->postJson("/api/both-service-form/module-requests/{$userAccess->id}/divisional-reject", [
                'divisional_director_name' => 'Test Divisional Director',
                'rejection_reason' => 'Rejected for testing purposes',
                'rejection_date' => '2024-01-15'
            ]);
        
        // Verify response is successful
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Request rejected by Divisional Director.'
            ]);
        
        // Verify database was updated
        $userAccess->refresh();
        $this->assertEquals('divisional_rejected', $userAccess->status);
        $this->assertEquals('Test Divisional Director', $userAccess->divisional_director_name);
        $this->assertNotNull($userAccess->divisional_approved_at);
        $this->assertEquals('Rejected for testing purposes', $userAccess->divisional_director_comments);
    }

    /**
     * Test that only divisional directors can access these endpoints
     */
    public function test_only_divisional_directors_can_approve(): void
    {
        $department = Department::create([
            'name' => 'Test Department',
            'code' => 'TEST'
        ]);
        
        // Create a HOD user instead of divisional director
        $hodUser = User::factory()->create([
            'name' => 'Test HOD',
            'email' => 'hod@example.com',
            'department_id' => $department->id
        ]);
        $hodUser->assignRole('head_of_department');
        
        $userAccess = UserAccess::create([
            'user_id' => User::factory()->create()->id,
            'pf_number' => 'TEST001',
            'staff_name' => 'Test Staff',
            'phone_number' => '1234567890',
            'department_id' => $department->id,
            'status' => 'hod_approved',
            'request_type' => ['wellsoft'],
            'wellsoft_modules_selected' => ['Registrar']
        ]);
        
        Storage::fake('public');
        $signatureFile = UploadedFile::fake()->image('signature.png');
        
        // Try to approve with non-divisional director user
        $response = $this->actingAs($hodUser, 'sanctum')
            ->postJson("/api/both-service-form/module-requests/{$userAccess->id}/divisional-approve", [
                'divisional_director_name' => 'Test HOD',
                'approved_date' => '2024-01-15',
                'comments' => 'Should not work',
                'divisional_director_signature' => $signatureFile
            ]);
        
        // Should be forbidden
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Access denied. Only Divisional Director can perform this action.'
            ]);
        
        // Verify database was not updated
        $userAccess->refresh();
        $this->assertEquals('hod_approved', $userAccess->status);
        $this->assertNull($userAccess->divisional_director_name);
    }
}
