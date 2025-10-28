<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add indexes to frequently queried columns in user_access table
     * to optimize query performance, especially for the show() method
     * and status filtering operations.
     */
    public function up(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Add indexes on status columns for faster filtering
            $table->index('hod_status', 'idx_user_access_hod_status');
            $table->index('divisional_status', 'idx_user_access_divisional_status');
            $table->index('ict_director_status', 'idx_user_access_ict_director_status');
            $table->index('head_it_status', 'idx_user_access_head_it_status');
            $table->index('ict_officer_status', 'idx_user_access_ict_officer_status');
            
            // Add indexes on foreign keys for faster joins
            $table->index('user_id', 'idx_user_access_user_id');
            $table->index('department_id', 'idx_user_access_department_id');
            
            // Add index on created_at for sorting and filtering by date
            $table->index('created_at', 'idx_user_access_created_at');
            
            // Composite index for common query patterns (status + department filtering)
            $table->index(['hod_status', 'department_id'], 'idx_user_access_hod_dept');
            $table->index(['divisional_status', 'created_at'], 'idx_user_access_div_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex('idx_user_access_div_created');
            $table->dropIndex('idx_user_access_hod_dept');
            $table->dropIndex('idx_user_access_created_at');
            $table->dropIndex('idx_user_access_department_id');
            $table->dropIndex('idx_user_access_user_id');
            $table->dropIndex('idx_user_access_ict_officer_status');
            $table->dropIndex('idx_user_access_head_it_status');
            $table->dropIndex('idx_user_access_ict_director_status');
            $table->dropIndex('idx_user_access_divisional_status');
            $table->dropIndex('idx_user_access_hod_status');
        });
    }
};
