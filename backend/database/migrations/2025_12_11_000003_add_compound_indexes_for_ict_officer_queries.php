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
        if (Schema::hasTable('ict_task_assignments')) {
            Schema::table('ict_task_assignments', function (Blueprint $table) {
                // Composite indexes to speed up whereHas filters in ICT Officer listing
                try { $table->index(['ict_officer_user_id', 'status'], 'ita_officer_status_index'); } catch (\Throwable $e) {}
                try { $table->index(['user_access_id', 'status'], 'ita_user_access_status_index'); } catch (\Throwable $e) {}
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
                    'ita_officer_status_index',
                    'ita_user_access_status_index',
                ] as $indexName) {
                    try { $table->dropIndex($indexName); } catch (\Throwable $e) {}
                }
            });
        }
    }
};
