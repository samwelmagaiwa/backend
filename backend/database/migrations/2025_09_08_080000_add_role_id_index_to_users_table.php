<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the users table and required columns exist before attempting to add indexes
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'role_id')) {
            return; // Skip safely if prerequisites are missing
        }

        $self = $this;

        Schema::table('users', function (Blueprint $table) use ($self) {
            // Add index on role_id for better performance (if it doesn't already exist)
            if (!$self->indexExists('users', 'users_role_id_index')) {
                $table->index('role_id');
            }

            // Add composite index for common queries if is_active column exists
            if (Schema::hasColumn('users', 'is_active') && !$self->indexExists('users', 'users_role_id_is_active_index')) {
                $table->index(['role_id', 'is_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $self = $this;

        Schema::table('users', function (Blueprint $table) use ($self) {
            // Drop composite index if it exists
            if ($self->indexExists('users', 'users_role_id_is_active_index')) {
                $table->dropIndex('users_role_id_is_active_index');
            }
            // Drop role_id index if it exists
            if ($self->indexExists('users', 'users_role_id_index')) {
                $table->dropIndex('users_role_id_index');
            }
        });
    }

    /**
     * Check if a given index exists on the table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $results = DB::select("SHOW INDEX FROM `{$table}` WHERE `Key_name` = ?", [$indexName]);
        return !empty($results);
    }
};

