<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\SimpleRoleAssignmentSeeder;

class AssignSimpleRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:assign-simple 
                            {--reset : Reset all existing role assignments before assigning new ones}
                            {--dry-run : Show what would be assigned without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign exactly one role to each user (7 users total, 1 role each)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Simple Role Assignment Tool');
        $this->info('Assigns exactly 7 users with 1 role each');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->performDryRun();
            return 0;
        }

        if ($this->option('reset') || $this->confirm('This will reset all existing role assignments. Continue?')) {
            $seeder = new SimpleRoleAssignmentSeeder();
            $seeder->setCommand($this);
            $seeder->run();
        } else {
            $this->info('Operation cancelled.');
        }

        return 0;
    }

    /**
     * Perform a dry run to show what would be assigned
     */
    private function performDryRun(): void
    {
        $this->warn('🔍 DRY RUN MODE - No changes will be made');
        $this->newLine();

        // Show current state
        $this->info('📊 Current Role Distribution:');
        $roles = \App\Models\Role::with('users')->get();
        
        foreach ($roles as $role) {
            $userCount = $role->users()->count();
            $this->line("  {$role->name}: {$userCount} users");
        }

        $this->newLine();
        
        // Show what would be assigned
        $this->info('🎯 Proposed Simple Assignment (7 users, 1 role each):');
        $this->line('  admin: 1 user (System Administrator)');
        $this->line('  divisional_director: 1 user (Director-level user)');
        $this->line('  head_of_department: 1 user (Head/Chief-level user)');
        $this->line('  ict_director: 1 user (ICT department user)');
        $this->line('  dict: 1 user (ICT department user)');
        $this->line('  ict_officer: 1 user (ICT department user)');
        $this->line('  staff: 1 user (Any remaining user)');

        $this->newLine();
        $this->info('💡 To execute this assignment, run:');
        $this->line('   php artisan roles:assign-simple --reset');
    }
}