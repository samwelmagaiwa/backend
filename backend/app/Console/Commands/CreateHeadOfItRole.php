<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class CreateHeadOfItRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create-head-of-it';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update the head_of_it role in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating/updating head_of_it role...');

        $role = Role::firstOrCreate(
            ['name' => 'head_of_it'],
            [
                'description' => 'Head of IT with approval authority for IT access requests',
                'permissions' => ['view_users', 'view_all_requests', 'approve_requests', 'reject_requests'],
                'is_system_role' => true,
                'is_deletable' => false,
                'sort_order' => 4
            ]
        );

        if ($role->wasRecentlyCreated) {
            $this->info("✅ Head of IT role created successfully!");
        } else {
            $this->info("✅ Head of IT role already exists.");
        }

        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $role->id],
                ['Name', $role->name],
                ['Description', $role->description],
                ['Permissions', implode(', ', $role->permissions ?? [])],
                ['System Role', $role->is_system_role ? 'Yes' : 'No'],
                ['Sort Order', $role->sort_order]
            ]
        );

        return Command::SUCCESS;
    }
}
