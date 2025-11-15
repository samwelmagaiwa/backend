<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Indexes for user_access table used heavily in timeline/access-requests flows
        Schema::table('user_access', function (Blueprint $table) {
            // Gate by Head of IT / ICT Officer status and foreign keys
            if (! $this->hasIndex('user_access', 'user_access_head_it_status_approved_at_idx')) {
                $table->index(['head_it_status', 'head_it_approved_at'], 'user_access_head_it_status_approved_at_idx');
            }

            if (! $this->hasIndex('user_access', 'user_access_ict_officer_status_user_idx')) {
                $table->index(['ict_officer_status', 'ict_officer_user_id'], 'user_access_ict_officer_status_user_idx');
            }

            if (! $this->hasIndex('user_access', 'user_access_department_id_idx')) {
                $table->index('department_id', 'user_access_department_id_idx');
            }

            if (! $this->hasIndex('user_access', 'user_access_created_at_idx')) {
                $table->index('created_at', 'user_access_created_at_idx');
            }
        });

        // Indexes for ict_task_assignments table used in ICT officer flows
        Schema::table('ict_task_assignments', function (Blueprint $table) {
            if (! $this->hasIndex('ict_task_assignments', 'ict_tasks_user_access_status_idx')) {
                $table->index(['user_access_id', 'status'], 'ict_tasks_user_access_status_idx');
            }

            if (! $this->hasIndex('ict_task_assignments', 'ict_tasks_officer_status_idx')) {
                $table->index(['ict_officer_user_id', 'status'], 'ict_tasks_officer_status_idx');
            }

            if (! $this->hasIndex('ict_task_assignments', 'ict_tasks_assigned_at_idx')) {
                $table->index('assigned_at', 'ict_tasks_assigned_at_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $this->dropIndexIfExists('user_access', 'user_access_head_it_status_approved_at_idx');
            $this->dropIndexIfExists('user_access', 'user_access_ict_officer_status_user_idx');
            $this->dropIndexIfExists('user_access', 'user_access_department_id_idx');
            $this->dropIndexIfExists('user_access', 'user_access_created_at_idx');
        });

        Schema::table('ict_task_assignments', function (Blueprint $table) {
            $this->dropIndexIfExists('ict_task_assignments', 'ict_tasks_user_access_status_idx');
            $this->dropIndexIfExists('ict_task_assignments', 'ict_tasks_officer_status_idx');
            $this->dropIndexIfExists('ict_task_assignments', 'ict_tasks_assigned_at_idx');
        });
    }

    /**
     * Helper to check if an index exists on a table.
     */
    private function hasIndex(string $table, string $index): bool
    {
        // Schema::hasIndex is available in recent Laravel; fall back to doctrine if needed.
        if (method_exists(Schema::class, 'hasIndex')) {
            return Schema::hasIndex($table, $index);
        }

        // Fallback: best-effort using connection schema manager (no-op if doctrine/dbal is missing)
        try {
            $connection = Schema::getConnection();
            $schemaManager = $connection->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            return array_key_exists($index, $indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Helper to drop index if it exists.
     */
    private function dropIndexIfExists(string $table, string $index): void
    {
        if (method_exists(Schema::class, 'hasIndex') && Schema::hasIndex($table, $index)) {
            Schema::table($table, function (Blueprint $table) use ($index) {
                $table->dropIndex($index);
            });
            return;
        }

        try {
            $connection = Schema::getConnection();
            $schemaManager = $connection->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            if (array_key_exists($index, $indexes)) {
                Schema::table($table, function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore in down() to avoid breaking rollback
        }
    }
};
