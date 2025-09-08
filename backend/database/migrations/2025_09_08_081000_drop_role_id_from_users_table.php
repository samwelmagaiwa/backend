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
        if (!Schema::hasTable('users')) {
            return;
        }

        // Drop composite and single indexes if they exist before dropping the column
        try {
            $indexes = DB::select("SHOW INDEX FROM `users`");
            $indexNames = collect($indexes)->pluck('Key_name')->toArray();

            Schema::table('users', function (Blueprint $table) use ($indexNames) {
                if (in_array('users_role_id_is_active_index', $indexNames)) {
                    $table->dropIndex('users_role_id_is_active_index');
                }
                if (in_array('users_role_id_index', $indexNames)) {
                    $table->dropIndex('users_role_id_index');
                }
                if (in_array('users_role_id_foreign', $indexNames)) {
                    // Some MySQL versions also list FKs in SHOW INDEX; just in case
                    $table->dropForeign('users_role_id_foreign');
                }
            });
        } catch (\Throwable $e) {
            // Ignore if index/foreign doesn't exist
        }

        // Drop FK first if column still present and FK exists
        if (Schema::hasColumn('users', 'role_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    // Drop by column shorthand (Laravel resolves constraint name)
                    $table->dropForeign(['role_id']);
                });
            } catch (\Throwable $e) {
                // FK might already be gone; ignore
            }
        }

        // Finally drop the column if it exists
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // Add role_id back (nullable) and recreate FK (optional)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('id');
            }
        });

        // Try to recreate FK constraint if roles table exists
        if (Schema::hasTable('roles')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
                // Ignore if cannot create FK
            }
        }

        // Optionally recreate the single index
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role_id');
            });
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};

