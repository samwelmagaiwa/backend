<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drop the deprecated general status column as the system now uses
     * role-specific status columns (hod_status, divisional_status, etc.)
     * and calculated overall status methods.
     */
    public function up(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop the deprecated general status column
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Restore the general status column for rollback purposes.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Restore the general status column
            $table->string('status')->nullable()->after('temporary_until');
        });
    }
};
