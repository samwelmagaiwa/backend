<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DynamicRoleAssignmentSeeder;

class AssignRolesDynamically extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:assign-dynamic 
                            {--reset : Reset all existing role assignments before assigning new ones}
                            {--dry-run : Show what would be assigned without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dynamically assign roles to users based on role names and department relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting dynamic role assignment...');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        if ($this->option('reset')) {
            if ($this->confirm('⚠️  This will reset ALL existing role assignments. Are you sure?')) {
                $this->resetExistingRoles();
            } else {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        if (!$this->option('dry-run')) {
            $seeder = new DynamicRoleAssignmentSeeder();
            $seeder->setCommand($this);
            $seeder->run();
        } else {
            $this->performDryRun();
        }

        $this->newLine();
        $this->info('✅ Dynamic role assignment completed!');
        
        return 0;
    }

    /**
     * Reset all existing role assignments
     */
    private function resetExistingRoles(): void
    {
        $this->info('🔄 Resetting existing role assignments...');
        
        // Clear many-to-many role assignments
        \DB::table('role_user')->delete();
        
        // Clear department leadership assignments
        \App\Models\Department::query()->update([
            'hod_user_id' => null,
            'divisional_director_id' => null,
            'has_divisional_director' => false
        ]);
        
        $this->info('✅ All role assignments have been reset.');
        $this->newLine();
    }

    /**
     * Perform a dry run to show what would be assigned
     */
    private function performDryRun(): void
    {
        $this->info('🔍 DRY RUN - Analyzing current state and potential assignments...');
        $this->newLine();

        // Show current role distribution
        $this->table(
            ['Role', 'Current Users', 'Users Without Roles'],
            $this->getCurrentRoleDistribution()
        );

        $this->newLine();
        $this->info('📋 Potential assignments based on dynamic logic:');
        $this->newLine();

        // Show potential assignments
        $this->showPotentialAssignments();
    }

    /**
     * Get current role distribution
     */
    private function getCurrentRoleDistribution(): array
    {
        $roles = \App\Models\Role::with('users')->get();
        $usersWithoutRoles = \App\Models\User::whereDoesntHave('roles')->count();
        
        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                $role->name,
                $role->users()->count(),
                $role->name === 'staff' ? $usersWithoutRoles : '-'
            ];
        }
        
        return $data;
    }

    /**
     * Show potential role assignments
     */
    private function showPotentialAssignments(): void
    {
        $departments = \App\Models\Department::with('users')->get();
        
        foreach ($departments as $department) {
            $this->info("🏢 {$department->name} Department:");
            
            $users = $department->users;
            if ($users->isEmpty()) {
                $this->warn("   No users in this department");
                continue;
            }

            foreach ($users as $user) {
                $currentRoles = $user->roles()->pluck('name')->toArray();
                $potentialRoles = $this->getPotentialRoles($user, $department);
                
                $this->line("   👤 {$user->name}:");
                $this->line("      Current: " . (empty($currentRoles) ? 'No roles' : implode(', ', $currentRoles)));
                $this->line("      Potential: " . implode(', ', $potentialRoles));
            }
            
            $this->newLine();
        }
    }

    /**
     * Get potential roles for a user
     */
    private function getPotentialRoles(\App\Models\User $user, \App\Models\Department $department): array
    {
        $potentialRoles = [];
        
        // Check if user could be admin (first user without roles)
        $usersWithoutRoles = \App\Models\User::whereDoesntHave('roles')->count();
        if ($usersWithoutRoles > 0 && !$user->roles()->exists()) {
            $adminExists = \App\Models\Role::where('name', 'admin')->first()?->users()->exists();
            if (!$adminExists) {
                $potentialRoles[] = 'admin (first available)';
            }
        }
        
        // Check department-based roles
        if ($department->code === 'ICT') {
            $potentialRoles[] = 'ict_officer';
            
            $ictDirectorExists = \App\Models\Role::where('name', 'ict_director')->first()?->users()->exists();
            if (!$ictDirectorExists) {
                $potentialRoles[] = 'ict_director';
            }
        }
        
        // Check if could be HOD
        if (!$department->hod_user_id) {
            $potentialRoles[] = 'head_of_department';
        }
        
        // Check if could be divisional director
        if (!$department->divisional_director_id) {
            $potentialRoles[] = 'divisional_director';
        }
        
        // Default to staff if no other roles
        if (empty($potentialRoles)) {
            $potentialRoles[] = 'staff';
        }
        
        return $potentialRoles;
    }
}