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
        // Only proceed if all required tables exist
        if (!Schema::hasTable('users') || !Schema::hasTable('roles') || !Schema::hasTable('role_user')) {
            return; // Skip if dependencies don't exist
        }

        DB::beginTransaction();
        
        try {
            // Find users without roles in the many-to-many system
            $usersWithoutRoles = User::whereDoesntHave('roles')->get();
            
            if ($usersWithoutRoles->count() > 0) {
                echo "Found {$usersWithoutRoles->count()} users without roles in many-to-many system\n";
                
                foreach ($usersWithoutRoles as $user) {
                    $roleToAssign = null;
                    
                    // Try to use the old role_id if it exists
                    if ($user->role_id) {
                        $oldRole = Role::find($user->role_id);
                        if ($oldRole) {
                            $roleToAssign = $oldRole;
                            echo "Migrating user {$user->email} from old role {$oldRole->name}\n";
                        }
                    }
                    
                    // If no old role, assign default 'staff' role
                    if (!$roleToAssign) {
                        $staffRole = Role::where('name', 'staff')->first();
                        if ($staffRole) {
                            $roleToAssign = $staffRole;
                            echo "Assigning default staff role to user {$user->email}\n";
                        }
                    }
                    
                    // If still no role, create a staff role
                    if (!$roleToAssign) {
                        $staffRole = Role::firstOrCreate(
                            ['name' => 'staff'],
                            [
                                'description' => 'Regular hospital staff member',
                                'permissions' => ['view_users'],
                                'is_system_role' => true,
                                'is_deletable' => false,
                                'sort_order' => 6
                            ]
                        );
                        $roleToAssign = $staffRole;
                        echo "Created and assigned staff role to user {$user->email}\n";
                    }
                    
                    // Assign the role
                    if ($roleToAssign) {
                        $user->roles()->attach($roleToAssign->id, [
                            'assigned_at' => $user->created_at ?? now(),
                            'assigned_by' => 1, // System migration
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        echo "✅ Successfully assigned role {$roleToAssign->name} to user {$user->email}\n";
                    }
                }
            } else {
                echo "✅ All users already have roles assigned\n";
            }
            
            DB::commit();
            echo "✅ User role assignment completed successfully\n";
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Error fixing user roles: " . $e->getMessage() . "\n";
            // Don't throw the exception to avoid breaking the migration
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