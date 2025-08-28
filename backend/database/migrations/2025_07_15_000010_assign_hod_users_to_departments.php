<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Department;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure required tables exist before running data migration
        if (!Schema::hasTable('users') || !Schema::hasTable('departments') || !Schema::hasTable('roles')) {
            throw new Exception('Required tables (users, departments, roles) must exist before assigning HOD users to departments.');
        }
        
        // Check if tables have data (seeded)
        if (User::count() === 0 || Department::count() === 0) {
            // Skip this migration if no data exists yet
            // This will be handled by seeders instead
            return;
        }
        
        try {
            // Get users for HOD assignments
            $hodUser = User::whereHas('role', function($query) {
                $query->where('name', 'head_of_department');
            })->first();
            
            $ictDirectorUser = User::whereHas('role', function($query) {
                $query->where('name', 'ict_director');
            })->first();

            // Assign HOD users to departments
            if ($hodUser) {
                Department::where('code', 'HR')->update(['hod_user_id' => $hodUser->id]);
            }
            
            if ($ictDirectorUser) {
                Department::where('code', 'ICT')->update(['hod_user_id' => $ictDirectorUser->id]);
            }
        } catch (Exception $e) {
            // Log the error but don't fail the migration
            error_log('Warning: Could not assign HOD users to departments: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run if tables exist
        if (Schema::hasTable('departments')) {
            try {
                // Remove HOD assignments
                Department::whereIn('code', ['HR', 'ICT'])->update(['hod_user_id' => null]);
            } catch (Exception $e) {
                error_log('Warning: Could not remove HOD assignments: ' . $e->getMessage());
            }
        }
    }
};