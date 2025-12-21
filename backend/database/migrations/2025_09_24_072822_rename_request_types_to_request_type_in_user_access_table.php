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
        // On some databases, the user_access table already has both
        // request_type (JSON) and request_types columns, or request_type
        // has already been created by an earlier migration. To avoid
        // "duplicate column" errors, only rename when request_types
        // exists and request_type does NOT.
        if (!Schema::hasTable('user_access')) {
            return;
        }

        $hasRequestTypes = Schema::hasColumn('user_access', 'request_types');
        $hasRequestType = Schema::hasColumn('user_access', 'request_type');

        if ($hasRequestTypes && !$hasRequestType) {
            Schema::table('user_access', function (Blueprint $table) {
                // Rename request_types column to request_type
                $table->renameColumn('request_types', 'request_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Revert back: rename request_type to request_types
            $table->renameColumn('request_type', 'request_types');
        });
    }
};
