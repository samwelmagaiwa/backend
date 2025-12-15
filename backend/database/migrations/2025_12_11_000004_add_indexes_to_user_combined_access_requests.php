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
        if (Schema::hasTable('user_combined_access_requests')) {
            Schema::table('user_combined_access_requests', function (Blueprint $table) {
                // Common filter/sort columns for Head of IT and earlier stages
                try { $table->index('department_id', 'ucar_department_id_index'); } catch (\Throwable $e) {}
                try { $table->index('user_id', 'ucar_user_id_index'); } catch (\Throwable $e) {}
                try { $table->index('created_at', 'ucar_created_at_index'); } catch (\Throwable $e) {}
                try { $table->index('updated_at', 'ucar_updated_at_index'); } catch (\Throwable $e) {}

                // Approval stages
                try { $table->index('hod_status', 'ucar_hod_status_index'); } catch (\Throwable $e) {}
                try { $table->index('divisional_status', 'ucar_divisional_status_index'); } catch (\Throwable $e) {}
                try { $table->index('ict_director_status', 'ucar_ict_director_status_index'); } catch (\Throwable $e) {}
                try { $table->index('head_it_status', 'ucar_head_it_status_index'); } catch (\Throwable $e) {}

                // Date columns used for FIFO ordering and analytics
                try { $table->index('hod_approved_at', 'ucar_hod_approved_at_index'); } catch (\Throwable $e) {}
                try { $table->index('divisional_approved_at', 'ucar_divisional_approved_at_index'); } catch (\Throwable $e) {}
                try { $table->index('ict_director_approved_at', 'ucar_ict_director_approved_at_index'); } catch (\Throwable $e) {}
                try { $table->index('head_of_it_approved_at', 'ucar_head_of_it_approved_at_index'); } catch (\Throwable $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_combined_access_requests')) {
            Schema::table('user_combined_access_requests', function (Blueprint $table) {
                foreach ([
                    'ucar_department_id_index',
                    'ucar_user_id_index',
                    'ucar_created_at_index',
                    'ucar_updated_at_index',
                    'ucar_hod_status_index',
                    'ucar_divisional_status_index',
                    'ucar_ict_director_status_index',
                    'ucar_head_it_status_index',
                    'ucar_hod_approved_at_index',
                    'ucar_divisional_approved_at_index',
                    'ucar_ict_director_approved_at_index',
                    'ucar_head_of_it_approved_at_index',
                ] as $indexName) {
                    try { $table->dropIndex($indexName); } catch (\Throwable $e) {}
                }
            });
        }
    }
};
