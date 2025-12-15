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
        // Indexes for ict_task_assignments used in ICT Officer listing filters
        if (Schema::hasTable('ict_task_assignments')) {
            Schema::table('ict_task_assignments', function (Blueprint $table) {
                try { $table->index('user_access_id', 'ita_user_access_id_index'); } catch (\Throwable $e) {}
                try { $table->index('ict_officer_user_id', 'ita_ict_officer_user_id_index'); } catch (\Throwable $e) {}
                try { $table->index('status', 'ita_status_index'); } catch (\Throwable $e) {}
                try { $table->index('assigned_at', 'ita_assigned_at_index'); } catch (\Throwable $e) {}
                try { $table->index('completed_at', 'ita_completed_at_index'); } catch (\Throwable $e) {}
            });
        }

        // Index for ordering/filters on user_access head_it_approved_at
        if (Schema::hasTable('user_access')) {
            Schema::table('user_access', function (Blueprint $table) {
                try { $table->index('head_it_approved_at', 'ua_head_it_approved_at_index'); } catch (\Throwable $e) {}
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ict_task_assignments')) {
            Schema::table('ict_task_assignments', function (Blueprint $table) {
                foreach ([
                    'ita_user_access_id_index',
                    'ita_ict_officer_user_id_index',
                    'ita_status_index',
                    'ita_assigned_at_index',
                    'ita_completed_at_index',
                ] as $indexName) {
                    try { $table->dropIndex($indexName); } catch (\Throwable $e) {}
                }
            });
        }

        if (Schema::hasTable('user_access')) {
            Schema::table('user_access', function (Blueprint $table) {
                try { $table->dropIndex('ua_head_it_approved_at_index'); } catch (\Throwable $e) {}
            });
        }
    }
};
