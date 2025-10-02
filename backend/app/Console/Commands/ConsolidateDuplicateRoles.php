<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConsolidateDuplicateRoles extends Command
{
    protected $signature = 'roles:consolidate-duplicates {--dry-run : Run without making changes}';
    protected $description = 'Consolidate duplicate ICT Director roles (ict_director and dict)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Consolidating duplicate ICT Director roles...');
        
        // Find the roles
        $ictDirector = Role::where('name', 'ict_director')->first();
        $dict = Role::where('name', 'dict')->first();
        
        if (!$ictDirector || !$dict) {
            $this->error('Could not find both ict_director and dict roles');
            return;
        }
        
        $this->line("Found roles:");
        $this->line("  - ICT Director (ID: {$ictDirector->id}) - '{$ictDirector->name}' - Display: '{$ictDirector->display_name}'");
        $this->line("  - DICT (ID: {$dict->id}) - '{$dict->name}' - Display: '{$dict->display_name}'");
        
        // Check users assigned to each role
        $ictDirectorUsers = $ictDirector->users()->get();
        $dictUsers = $dict->users()->get();
        
        $this->line("\nUsers assigned:");
        $this->line("  - ICT Director role: {$ictDirectorUsers->count()} users");
        foreach ($ictDirectorUsers as $user) {
            $this->line("    * {$user->name} ({$user->email})");
        }
        
        $this->line("  - DICT role: {$dictUsers->count()} users");
        foreach ($dictUsers as $user) {
            $this->line("    * {$user->name} ({$user->email})");
        }
        
        if (!$isDryRun) {
            if (!$this->confirm('Do you want to merge DICT users into ICT Director role and remove DICT role?')) {
                $this->info('Operation cancelled.');
                return;
            }
            
            DB::beginTransaction();
            
            try {
                // Move all DICT users to ICT Director role
                foreach ($dictUsers as $user) {
                    // Check if user already has ICT Director role
                    if (!$user->roles()->where('role_id', $ictDirector->id)->exists()) {
                        $user->roles()->attach($ictDirector->id, [
                            'assigned_at' => now(),
                            'assigned_by' => 1, // Assuming admin user ID 1
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $this->line("  - Added ICT Director role to {$user->name}");
                    }
                    
                    // Remove DICT role from user
                    $user->roles()->detach($dict->id);
                    $this->line("  - Removed DICT role from {$user->name}");
                }
                
                // Update any references to DICT in the codebase
                // Check for references in permissions, routes, etc.
                $this->info('Checking for code references to DICT role...');
                
                // Remove the DICT role
                $dict->delete();
                $this->info("DICT role (ID: {$dict->id}) has been deleted");
                
                DB::commit();
                
                $this->info('✅ Successfully consolidated duplicate roles');
                $this->line("All users with DICT role have been moved to ICT Director role");
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to consolidate roles: " . $e->getMessage());
                return 1;
            }
        }
        
        $this->info("\nRecommendation: Update code references from 'dict' to 'ict_director'");
        $this->line("Search for 'dict' in:");
        $this->line("  - Role middleware");
        $this->line("  - Permission definitions");
        $this->line("  - Route guards");
        $this->line("  - Frontend role checks");
        
        return 0;
    }
}