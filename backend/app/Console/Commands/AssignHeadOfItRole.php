<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignHeadOfItRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-head-of-it {email : The email of the user to assign the role to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign head_of_it role to a specific user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Looking for user with email: {$email}");
        
        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return Command::FAILURE;
        }
        
        $this->info("Found user: {$user->name} (ID: {$user->id})");
        
        // Find the head_of_it role
        $role = Role::where('name', 'head_of_it')->first();
        
        if (!$role) {
            $this->error('head_of_it role not found. Run php artisan role:create-head-of-it first.');
            return Command::FAILURE;
        }
        
        $this->info("Found head_of_it role (ID: {$role->id})");
        
        // Check if user already has the role
        if ($user->hasRole('head_of_it')) {
            $this->info('User already has the head_of_it role.');
        } else {
            // Assign the role
            $user->roles()->attach($role->id, [
                'assigned_at' => now(),
                'assigned_by' => 1 // Assuming admin user has ID 1
            ]);
            
            $this->info('✅ Successfully assigned head_of_it role to user.');
        }
        
        // Show user's current roles
        $userRoles = $user->roles->pluck('name')->toArray();
        $this->table(['User Roles'], array_map(fn($role) => [$role], $userRoles));
        
        // Reset onboarding if needed
        if ($this->confirm('Reset onboarding for this user?', false)) {
            $user->onboarding()->delete();
            $this->info('✅ Onboarding reset for user.');
        }
        
        return Command::SUCCESS;
    }
}
