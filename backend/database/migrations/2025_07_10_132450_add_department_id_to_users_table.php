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
        // Ensure both tables exist before proceeding
        if (!Schema::hasTable('users') || !Schema::hasTable('departments')) {
            return; // Skip if dependencies don't exist
        }
        
        Schema::table('users', function (Blueprint $table) {
            // Add department_id field if it doesn't exist
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('phone')
                    ->constrained('departments')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint and column if they exist
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropIndex(['department_id']);
                $table->dropColumn('department_id');
            }
        });
    }
};