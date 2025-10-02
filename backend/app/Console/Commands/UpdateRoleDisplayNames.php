<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class UpdateRoleDisplayNames extends Command
{
    protected $signature = 'roles:update-display-names';
    protected $description = 'Update display names for roles that have null display_name';

    public function handle()
    {
        $this->info('Checking roles with null display_name...');
        
        $roles = Role::whereNull('display_name')->orWhere('display_name', '')->get();
        
        if ($roles->isEmpty()) {
            $this->info('All roles already have display names set.');
            return;
        }
        
        $this->info("Found {$roles->count()} roles with missing display names.");
        
        foreach ($roles as $role) {
            // Convert snake_case to Title Case
            $displayName = ucwords(str_replace('_', ' ', $role->name));
            
            $role->update(['display_name' => $displayName]);
            
            $this->line("Updated role '{$role->name}' -> '{$displayName}'");
        }
        
        $this->info('All role display names updated successfully!');
        
        // Show all roles for verification
        $this->info("\nAll roles:");
        $allRoles = Role::select('id', 'name', 'display_name')->get();
        
        foreach ($allRoles as $role) {
            $this->line("ID: {$role->id} | Name: {$role->name} | Display: {$role->display_name}");
        }
    }
}