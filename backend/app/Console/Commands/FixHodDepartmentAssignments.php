<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;

class FixHodDepartmentAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hod:fix-departments {--check : Only check current state without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix HOD department assignments and diagnose issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnosing HOD Department Assignments...');
        $this->newLine();

        // Check if roles exist
        $this->info('📋 Checking Roles:');
        $roles = Role::all();
        if ($roles->isEmpty()) {
            $this->error('❌ No roles found in database!');
            return 1;
        }

        foreach ($roles as $role) {
            $userCount = User::where('role_id', $role->id)->count();
            $this->line("   • {$role->name}: {$userCount} users");
        }
        $this->newLine();

        // Check HOD users
        $this->info('👥 Checking HOD Users:');
        $hodUsers = User::whereHas('role', function($query) {
            $query->whereIn('name', ['head_of_department', 'ict_director']);
        })->with('role')->get();

        if ($hodUsers->isEmpty()) {
            $this->error('❌ No HOD users found!');
            return 1;
        }

        foreach ($hodUsers as $user) {
            $this->line("   • ID: {$user->id}, Email: {$user->email}, Role: {$user->role->name}");
        }
        $this->newLine();

        // Check departments
        $this->info('🏢 Checking Departments:');
        $departments = Department::all();
        if ($departments->isEmpty()) {
            $this->error('❌ No departments found!');
            return 1;
        }

        foreach ($departments as $dept) {
            $hodInfo = $dept->hod_user_id ? "HOD: User #{$dept->hod_user_id}" : "No HOD assigned";
            $this->line("   • {$dept->name} ({$dept->code}): {$hodInfo}");
        }
        $this->newLine();

        // Check current assignments
        $this->info('🔗 Current HOD-Department Assignments:');
        $assignedDepartments = Department::whereNotNull('hod_user_id')->with('headOfDepartment.role')->get();
        
        if ($assignedDepartments->isEmpty()) {
            $this->warn('⚠️  No departments have HOD assignments!');
        } else {
            foreach ($assignedDepartments as $dept) {
                $hod = $dept->headOfDepartment;
                $this->line("   • {$dept->name}: {$hod->name} ({$hod->email}) - Role: {$hod->role->name}");
            }
        }
        $this->newLine();

        // If only checking, stop here
        if ($this->option('check')) {
            $this->info('✅ Check complete. Use without --check flag to apply fixes.');
            return 0;
        }

        // Apply fixes
        $this->info('🔧 Applying Fixes:');
        
        // Get specific users
        $hodUser = User::whereHas('role', function($query) {
            $query->where('name', 'head_of_department');
        })->first();
        
        $ictDirectorUser = User::whereHas('role', function($query) {
            $query->where('name', 'ict_director');
        })->first();
        
        $hodItUser = User::whereHas('role', function($query) {
            $query->where('name', 'ict_director');
        })->first();

        $fixed = 0;

        // Assign HOD to HR department
        if ($hodUser) {
            $hrDept = Department::where('code', 'HR')->first();
            if ($hrDept) {
                $hrDept->update(['hod_user_id' => $hodUser->id]);
                $this->info("   ✅ Assigned {$hodUser->name} as HOD of {$hrDept->name}");
                $fixed++;
            } else {
                $this->warn("   ⚠️  HR department not found");
            }
        } else {
            $this->warn("   ⚠️  head_of_department user not found");
        }

        // Assign ICT Director to ICT department
        if ($ictDirectorUser) {
            $ictDept = Department::where('code', 'ICT')->first();
            if ($ictDept) {
                $ictDept->update(['hod_user_id' => $ictDirectorUser->id]);
                $this->info("   ✅ Assigned {$ictDirectorUser->name} as HOD of {$ictDept->name}");
                $fixed++;
            } else {
                $this->warn("   ⚠️  ICT department not found");
            }
        } else {
            $this->warn("   ⚠️  ict_director user not found");
        }

        // Assign HOD IT to a department (optional)
        if ($hodItUser) {
            $adminDept = Department::where('code', 'ADMIN')->first();
            if ($adminDept && !$adminDept->hod_user_id) {
                $adminDept->update(['hod_user_id' => $hodItUser->id]);
                $this->info("   ✅ Assigned {$hodItUser->name} as HOD of {$adminDept->name}");
                $fixed++;
            }
        }

        $this->newLine();
        $this->info("🎉 Fixed {$fixed} department assignments!");
        
        // Show final state
        $this->info('📊 Final State:');
        $finalAssignments = Department::whereNotNull('hod_user_id')->with('headOfDepartment.role')->get();
        foreach ($finalAssignments as $dept) {
            $hod = $dept->headOfDepartment;
            $this->line("   • {$dept->name}: {$hod->name} ({$hod->email}) - Role: {$hod->role->name}");
        }

        return 0;
    }
}