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
        // Get users for HOD assignments
        $hodUser = User::whereHas('role', function($query) {
            $query->where('name', 'head_of_department');
        })->first();
        
        $hodItUser = User::whereHas('role', function($query) {
            $query->where('name', 'hod_it');
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
        
        if ($hodItUser) {
            // You can assign HOD IT to another department if needed
            // Department::where('code', 'SOME_DEPT')->update(['hod_user_id' => $hodItUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove HOD assignments
        Department::whereIn('code', ['HR', 'ICT'])->update(['hod_user_id' => null]);
    }
};