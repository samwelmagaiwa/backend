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
        Schema::table('user_access', function (Blueprint $table) {
            // Add Head of IT approval/rejection fields
            $table->string('request_id')->nullable()->after('id'); // Generate unique request IDs
            $table->string('department_name')->nullable()->after('department_id'); // Cache department name
            $table->string('position')->nullable()->after('staff_name'); // Staff position
            
            // Head of IT approval fields
            $table->timestamp('head_of_it_approved_at')->nullable()->after('ict_director_approved_at');
            $table->unsignedBigInteger('head_of_it_approved_by')->nullable()->after('head_of_it_approved_at');
            $table->string('head_of_it_signature_path')->nullable()->after('head_of_it_approved_by');
            $table->text('head_of_it_comments')->nullable()->after('head_of_it_signature_path');
            
            // Head of IT rejection fields
            $table->timestamp('head_of_it_rejected_at')->nullable()->after('head_of_it_comments');
            $table->unsignedBigInteger('head_of_it_rejected_by')->nullable()->after('head_of_it_rejected_at');
            $table->text('head_of_it_rejection_reason')->nullable()->after('head_of_it_rejected_by');
            
            // Task assignment fields
            $table->unsignedBigInteger('assigned_ict_officer_id')->nullable()->after('head_of_it_rejection_reason');
            $table->timestamp('task_assigned_at')->nullable()->after('assigned_ict_officer_id');
            
            // Request types field (for multiple request types)
            $table->text('request_types')->nullable()->after('request_type'); // Store as JSON or comma-separated
            
            // Add foreign key constraints
            $table->foreign('head_of_it_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('head_of_it_rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_ict_officer_id')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index(['request_id']);
            $table->index(['head_of_it_approved_at']);
            $table->index(['head_of_it_rejected_at']);
            $table->index(['assigned_ict_officer_id']);
            $table->index(['task_assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['head_of_it_approved_by']);
            $table->dropForeign(['head_of_it_rejected_by']);
            $table->dropForeign(['assigned_ict_officer_id']);
            
            // Drop indexes
            $table->dropIndex(['request_id']);
            $table->dropIndex(['head_of_it_approved_at']);
            $table->dropIndex(['head_of_it_rejected_at']);
            $table->dropIndex(['assigned_ict_officer_id']);
            $table->dropIndex(['task_assigned_at']);
            
            // Drop columns
            $table->dropColumn([
                'request_id',
                'department_name',
                'position',
                'head_of_it_approved_at',
                'head_of_it_approved_by',
                'head_of_it_signature_path',
                'head_of_it_comments',
                'head_of_it_rejected_at',
                'head_of_it_rejected_by',
                'head_of_it_rejection_reason',
                'assigned_ict_officer_id',
                'task_assigned_at',
                'request_types'
            ]);
        });
    }
};
