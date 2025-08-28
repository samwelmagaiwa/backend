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
        Schema::table('roles', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('roles', 'display_name')) {
                $table->string('display_name')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable()->after('display_name');
            }
            
            if (!Schema::hasColumn('roles', 'permissions')) {
                $table->json('permissions')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('roles', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('permissions');
            }
            
            if (!Schema::hasColumn('roles', 'is_system_role')) {
                $table->boolean('is_system_role')->default(false)->after('sort_order');
            }
            
            if (!Schema::hasColumn('roles', 'is_deletable')) {
                $table->boolean('is_deletable')->default(true)->after('is_system_role');
            }
        });

        // Add indexes if they don't exist
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->index('sort_order');
                $table->index('is_system_role');
                $table->index('is_deletable');
            });
        } catch (\Exception $e) {
            // Indexes might already exist, ignore the error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Drop indexes first
            try {
                $table->dropIndex(['sort_order']);
                $table->dropIndex(['is_system_role']);
                $table->dropIndex(['is_deletable']);
            } catch (\Exception $e) {
                // Ignore if indexes don't exist
            }

            // Drop columns if they exist
            $columnsToCheck = ['display_name', 'description', 'permissions', 'sort_order', 'is_system_role', 'is_deletable'];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('roles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};