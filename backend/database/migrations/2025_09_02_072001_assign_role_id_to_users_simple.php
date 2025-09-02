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
     * 
     * This migration ensures all users have a role_id assigned based on their
     * primary role from the many-to-many relationship.
     */
    public function up(): void
    {
        // Get all users without role_id
        $users = User::with('roles')->whereNull('role_id')->get();
        
        foreach ($users as $user) {
            // Get user's primary role from many-to-many relationship
            $primaryRole = $user->getPrimaryRole();
            
            if ($primaryRole) {
                // Assign the primary role's ID to role_id field
                $user->update(['role_id' => $primaryRole->id]);
            } else {
                // If user has no roles, assign staff role
                $staffRole = Role::where('name', 'staff')->first();
                if ($staffRole) {
                    // Assign staff role via many-to-many
                    $user->roles()->attach($staffRole->id, [
                        'assigned_at' => now(),
                        'assigned_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Assign staff role_id
                    $user->update(['role_id' => $staffRole->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear role_id assignments
        DB::table('users')->update(['role_id' => null]);
    }
};