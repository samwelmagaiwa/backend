<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add common indexes to speed up listing/filtering/sorting queries
        Schema::table('user_access', function (Blueprint $table) {
            // Guard against duplicate index names by using explicit names
            try { $table->index('department_id', 'ua_department_id_index'); } catch (\Throwable $e) {}
            try { $table->index('user_id', 'ua_user_id_index'); } catch (\Throwable $e) {}
            try { $table->index('created_at', 'ua_created_at_index'); } catch (\Throwable $e) {}
            try { $table->index('updated_at', 'ua_updated_at_index'); } catch (\Throwable $e) {}

            // Approval workflow status columns (if present)
            try { $table->index('hod_status', 'ua_hod_status_index'); } catch (\Throwable $e) {}
            try { $table->index('divisional_status', 'ua_divisional_status_index'); } catch (\Throwable $e) {}
            try { $table->index('ict_director_status', 'ua_ict_director_status_index'); } catch (\Throwable $e) {}
            try { $table->index('head_it_status', 'ua_head_it_status_index'); } catch (\Throwable $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop indexes if they exist; ignore errors if they don't
            foreach ([
                'ua_department_id_index',
                'ua_user_id_index',
                'ua_created_at_index',
                'ua_updated_at_index',
                'ua_hod_status_index',
                'ua_divisional_status_index',
                'ua_ict_director_status_index',
                'ua_head_it_status_index',
            ] as $indexName) {
                try { $table->dropIndex($indexName); } catch (\Throwable $e) {}
            }
        });
    }
};
