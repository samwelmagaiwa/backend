<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReorganizeRoleIds extends Command
{
    protected $signature = 'roles:reorganize-ids {--dry-run : Run without making changes}';
    protected $description = 'Reorganize role IDs to remove gaps after role deletion';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Reorganizing role IDs...');
        
        // Get all roles ordered by current ID
        $roles = Role::orderBy('id')->get();
        
        if ($roles->isEmpty()) {
            $this->error('No roles found in the database');
            return;
        }
        
        $this->info("Current roles:");
        foreach ($roles as $role) {
            $userCount = $role->users()->count();
            $this->line("  ID: {$role->id} | Name: {$role->name} | Display: {$role->display_name} | Users: {$userCount}");
        }
        
        // Check if reorganization is needed
        $expectedId = 1;
        $needsReorganization = false;
        
        foreach ($roles as $role) {
            if ($role->id !== $expectedId) {
                $needsReorganization = true;
                break;
            }
            $expectedId++;
        }
        
        if (!$needsReorganization) {
            $this->info('Role IDs are already properly organized. No changes needed.');
            return;
        }
        
        $this->info("\nRole ID reorganization is needed.");
        
        if (!$isDryRun) {
            if (!$this->confirm('Do you want to proceed with reorganizing role IDs?')) {
                $this->info('Operation cancelled.');
                return;
            }
            
            DB::beginTransaction();
            
            try {
                // Temporarily disable foreign key checks
                DB::statement('SET foreign_key_checks = 0');
                
                $mapping = [];
                $newId = 1;
                
                // Create a mapping of old ID to new ID
                foreach ($roles as $role) {
                    if ($role->id !== $newId) {
                        $mapping[$role->id] = $newId;
                    }
                    $newId++;
                }
                
                if (!empty($mapping)) {
                    $this->info("\nID mapping:");
                    foreach ($mapping as $oldId => $newId) {
                        $roleName = $roles->where('id', $oldId)->first()->name;
                        $this->line("  Role '{$roleName}': {$oldId} -> {$newId}");
                    }
                    
                    // Update role_user pivot table
                    foreach ($mapping as $oldId => $newId) {
                        DB::table('role_user')
                            ->where('role_id', $oldId)
                            ->update(['role_id' => $newId]);
                        $this->line("Updated role_user pivot table: role_id {$oldId} -> {$newId}");
                    }
                    
                    // Update role_change_logs table if it exists
                    if (DB::getSchemaBuilder()->hasTable('role_change_logs')) {
                        foreach ($mapping as $oldId => $newId) {
                            DB::table('role_change_logs')
                                ->where('role_id', $oldId)
                                ->update(['role_id' => $newId]);
                            $this->line("Updated role_change_logs table: role_id {$oldId} -> {$newId}");
                        }
                    }
                    
                    // Update the roles table itself
                    foreach ($mapping as $oldId => $newId) {
                        DB::table('roles')
                            ->where('id', $oldId)
                            ->update(['id' => $newId]);
                        $this->line("Updated roles table: id {$oldId} -> {$newId}");
                    }
                    
                    // Reset auto increment to the next available ID
                    $nextId = $roles->count() + 1;
                    DB::statement("ALTER TABLE roles AUTO_INCREMENT = {$nextId}");
                    $this->line("Reset AUTO_INCREMENT to {$nextId}");
                }
                
                // Re-enable foreign key checks
                DB::statement('SET foreign_key_checks = 1');
                
                DB::commit();
                
                // Show final result
                $this->info("\nFinal roles:");
                $finalRoles = Role::orderBy('id')->get();
                foreach ($finalRoles as $role) {
                    $userCount = $role->users()->count();
                    $this->line("  ID: {$role->id} | Name: {$role->name} | Display: {$role->display_name} | Users: {$userCount}");
                }
                
                $this->info('✅ Role IDs reorganized successfully!');
                
            } catch (\Exception $e) {
                try {
                    DB::rollBack();
                } catch (\Exception $rollbackException) {
                    // Transaction may have already been closed
                }
                $this->error("Failed to reorganize role IDs: " . $e->getMessage());
                return 1;
            }
        } else {
            $this->info("\nProposed changes (DRY RUN):");
            $newId = 1;
            foreach ($roles as $role) {
                if ($role->id !== $newId) {
                    $this->line("  Role '{$role->name}' would change: ID {$role->id} -> {$newId}");
                } else {
                    $this->line("  Role '{$role->name}' would remain: ID {$role->id}");
                }
                $newId++;
            }
        }
        
        return 0;
    }
}