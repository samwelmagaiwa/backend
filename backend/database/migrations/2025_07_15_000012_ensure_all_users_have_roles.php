<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure required tables exist
        if (!Schema::hasTable('users') || !Schema::hasTable('roles') || !Schema::hasTable('role_user')) {
            // Skip migration if required tables don't exist yet
            return;
        }
        
        // Check if there's data to process
        if (User::count() === 0) {
            // No users to process, skip
            return;
        }
        
        DB::beginTransaction();
        
        try {
            // Ensure all users have roles in the new many-to-many system
            $users = User::with('role')->get();
            
            foreach ($users as $user) {
                // Check if user already has roles in the new system
                $existingRoles = $user->roles()->count();
                
                if ($existingRoles === 0) {
                    // If user has old role_id, migrate it
                    if ($user->role_id && $user->role) {
                        $user->roles()->attach($user->role_id, [
                            'assigned_at' => $user->created_at ?? now(),
                            'assigned_by' => 1, // Assume system migration
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        echo "Migrated user {$user->email} from role {$user->role->name}\n";
                    } else {
                        // Assign default 'staff' role if no role exists
                        $staffRole = Role::where('name', 'staff')->first();
                        if ($staffRole) {
                            $user->roles()->attach($staffRole->id, [
                                'assigned_at' => $user->created_at ?? now(),
                                'assigned_by' => 1, // Assume system migration
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            echo "Assigned default staff role to user {$user->email}\n";
                        }
                    }
                }
            }
            
            // Update role sort orders if not set
            $roles = Role::whereNull('sort_order')->orWhere('sort_order', 0)->get();
            $sortOrder = 1;
            
            foreach ($roles as $role) {
                $role->update(['sort_order' => $sortOrder]);
                $sortOrder++;
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error but don't fail the migration
            error_log('Warning: Could not ensure all users have roles: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it ensures data consistency
        // Removing role assignments could break the system
    }
};