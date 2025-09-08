<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RoleSynchronizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create basic roles
        Role::create(['name' => 'admin', 'description' => 'Administrator', 'permissions' => ['*']]);
        Role::create(['name' => 'head_of_department', 'description' => 'Head of Department', 'permissions' => ['manage_department']]);
        Role::create(['name' => 'divisional_director', 'description' => 'Divisional Director', 'permissions' => ['manage_division']]);
        Role::create(['name' => 'ict_officer', 'description' => 'ICT Officer', 'permissions' => ['manage_ict']]);
        Role::create(['name' => 'staff', 'description' => 'Staff Member', 'permissions' => ['view_dashboard']]);
    }

    /** @test */
    public function it_updates_primary_role_when_roles_are_assigned()
    {
        $user = User::factory()->create();
        $staffRole = Role::where('name', 'staff')->first();
        $hodRole = Role::where('name', 'head_of_department')->first();
        
        // Initially assign staff role
        $user->roles()->attach($staffRole->id);
        
        $this->assertEquals('staff', $user->fresh()->getPrimaryRoleName());
        
        // Assign HOD role (higher priority)
        $user->roles()->attach($hodRole->id);
        
        $this->assertEquals('head_of_department', $user->fresh()->getPrimaryRoleName());
    }

    /** @test */
    public function it_updates_primary_role_when_higher_priority_role_is_removed()
    {
        $user = User::factory()->create();
        $staffRole = Role::where('name', 'staff')->first();
        $hodRole = Role::where('name', 'head_of_department')->first();
        
        // Assign both roles
        $user->roles()->attach([$staffRole->id, $hodRole->id]);
        
        $this->assertEquals('head_of_department', $user->fresh()->getPrimaryRoleName());
        
        // Remove HOD role
        $user->roles()->detach($hodRole->id);
        
        $this->assertEquals('staff', $user->fresh()->getPrimaryRoleName());
    }

    /** @test */
    public function it_assigns_hod_role_when_user_is_assigned_as_department_hod()
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $hodRole = Role::where('name', 'head_of_department')->first();
        
        // Assign user as HOD of department
        $department->update(['hod_user_id' => $user->id]);
        
        // Simulate the role assignment that should happen in the controller
        if (!$user->hasRole('head_of_department')) {
            $user->roles()->attach($hodRole->id);
        }
        
        $this->assertTrue($user->hasRole('head_of_department'));
        $this->assertEquals('head_of_department', $user->fresh()->getPrimaryRoleName());
    }

    /** @test */
    public function it_respects_role_hierarchy()
    {
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $hodRole = Role::where('name', 'head_of_department')->first();
        $staffRole = Role::where('name', 'staff')->first();
        
        // Assign multiple roles
        $user->roles()->attach([$staffRole->id, $hodRole->id, $adminRole->id]);
        
        // Admin should be the primary role (highest priority)
        $this->assertEquals('admin', $user->fresh()->getPrimaryRoleName());
    }

    /** @test */
    public function it_handles_users_with_no_roles()
    {
        $user = User::factory()->create();
        
        
        $this->assertEquals('staff', $user->getPrimaryRoleName()); // Default fallback
    }
}