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
        
        // Check if there's data to migrate
        if (User::count() === 0) {
            // No users to migrate, skip
            return;
        }
        
        DB::beginTransaction();
        
        try {
            // Migrate existing users from single role to many-to-many roles
            $users = User::with('role')->get();
            
            foreach ($users as $user) {
                if ($user->role_id && $user->role) {
                    // Check if user already has roles in the new system
                    $existingRoles = $user->roles()->count();
                    
                    if ($existingRoles === 0) {
                        // Assign the old role to the new many-to-many system
                        $user->roles()->attach($user->role_id, [
                            'assigned_at' => now(),
                            'assigned_by' => 1, // Assume admin user has ID 1
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        echo "Migrated user {$user->email} from role {$user->role->name}\n";
                    }
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error but don't fail the migration
            error_log('Warning: Could not migrate existing roles: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run if table exists
        if (Schema::hasTable('role_user')) {
            try {
                // Remove all role assignments from the many-to-many table
                DB::table('role_user')->truncate();
            } catch (Exception $e) {
                error_log('Warning: Could not truncate role_user table: ' . $e->getMessage());
            }
        }
    }
};