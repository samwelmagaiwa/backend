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
        // Update roles table with additional fields
        Schema::table('roles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->json('permissions')->nullable()->after('description');
            $table->boolean('is_system_role')->default(false)->after('permissions');
            $table->boolean('is_deletable')->default(true)->after('is_system_role');
            $table->integer('sort_order')->default(0)->after('is_deletable');
        });

        // Create role_user pivot table for many-to-many relationship
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['user_id', 'role_id']);
            
            // Indexes for performance
            $table->index(['user_id', 'assigned_at']);
            $table->index(['role_id', 'assigned_at']);
        });

        // Update departments table to ensure proper HOD assignment
        Schema::table('departments', function (Blueprint $table) {
            // Add index for better performance
            $table->index('hod_user_id');
            
            // Add additional fields for better department management
            if (!Schema::hasColumn('departments', 'parent_department_id')) {
                $table->foreignId('parent_department_id')->nullable()->constrained('departments')->onDelete('set null');
            }
            if (!Schema::hasColumn('departments', 'level')) {
                $table->integer('level')->default(1);
            }
        });

        // Create role_change_logs table for audit trail
        Schema::create('role_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['assigned', 'removed']);
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable(); // Store additional context
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            // Indexes for audit queries
            $table->index(['user_id', 'changed_at']);
            $table->index(['role_id', 'changed_at']);
            $table->index(['changed_by', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_change_logs');
        Schema::dropIfExists('role_user');
        
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['hod_user_id']);
            if (Schema::hasColumn('departments', 'parent_department_id')) {
                $table->dropForeign(['parent_department_id']);
                $table->dropColumn('parent_department_id');
            }
            if (Schema::hasColumn('departments', 'level')) {
                $table->dropColumn('level');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'permissions',
                'is_system_role',
                'is_deletable',
                'sort_order'
            ]);
        });
    }
};