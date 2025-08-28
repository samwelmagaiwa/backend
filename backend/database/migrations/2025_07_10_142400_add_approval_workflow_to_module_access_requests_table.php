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
        Schema::table('module_access_requests', function (Blueprint $table) {
            // Add personal information fields (auto-populated)
            $table->string('pf_number')->nullable()->after('user_id');
            $table->string('staff_name')->nullable()->after('pf_number');
            $table->string('phone_number')->nullable()->after('staff_name');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null')->after('phone_number');
            $table->string('signature_path')->nullable()->after('department_id');
            
            // Add form type to distinguish both-service-form from regular module access requests
            $table->enum('form_type', ['module_access', 'both_service_form'])->default('module_access')->after('signature_path');
            
            // Add services requested (for both-service-form)
            $table->json('services_requested')->nullable()->after('form_type'); // ['wellsoft', 'jeeva', 'internet_access']
            
            // HOD Approval (required)
            $table->enum('hod_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('services_requested');
            $table->text('hod_comments')->nullable()->after('hod_approval_status');
            $table->string('hod_signature_path')->nullable()->after('hod_comments');
            $table->timestamp('hod_approved_at')->nullable()->after('hod_signature_path');
            $table->foreignId('hod_user_id')->nullable()->constrained('users')->onDelete('set null')->after('hod_approved_at');
            
            // Divisional Director Approval
            $table->enum('divisional_director_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('hod_user_id');
            $table->text('divisional_director_comments')->nullable()->after('divisional_director_approval_status');
            $table->string('divisional_director_signature_path')->nullable()->after('divisional_director_comments');
            $table->timestamp('divisional_director_approved_at')->nullable()->after('divisional_director_signature_path');
            $table->foreignId('divisional_director_user_id')->nullable()->constrained('users')->onDelete('set null')->after('divisional_director_approved_at');
            
            // DICT Approval
            $table->enum('dict_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('divisional_director_user_id');
            $table->text('dict_comments')->nullable()->after('dict_approval_status');
            $table->string('dict_signature_path')->nullable()->after('dict_comments');
            $table->timestamp('dict_approved_at')->nullable()->after('dict_signature_path');
            $table->foreignId('dict_user_id')->nullable()->constrained('users')->onDelete('set null')->after('dict_approved_at');
            
            // Head of IT Approval
            $table->enum('hod_it_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('dict_user_id');
            $table->text('hod_it_comments')->nullable()->after('hod_it_approval_status');
            $table->string('hod_it_signature_path')->nullable()->after('hod_it_comments');
            $table->timestamp('hod_it_approved_at')->nullable()->after('hod_it_signature_path');
            $table->foreignId('hod_it_user_id')->nullable()->constrained('users')->onDelete('set null')->after('hod_it_approved_at');
            
            // ICT Officer Approval (final)
            $table->enum('ict_officer_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('hod_it_user_id');
            $table->text('ict_officer_comments')->nullable()->after('ict_officer_approval_status');
            $table->string('ict_officer_signature_path')->nullable()->after('ict_officer_comments');
            $table->timestamp('ict_officer_approved_at')->nullable()->after('ict_officer_signature_path');
            $table->foreignId('ict_officer_user_id')->nullable()->constrained('users')->onDelete('set null')->after('ict_officer_approved_at');
            
            // Overall status tracking
            $table->enum('overall_status', ['pending', 'in_review', 'approved', 'rejected'])->default('pending')->after('ict_officer_user_id');
            $table->string('current_approval_stage')->default('hod')->after('overall_status'); // hod, divisional_director, dict, hod_it, ict_officer, completed
            
            // Add indexes for better performance
            $table->index(['form_type', 'overall_status']);
            $table->index(['current_approval_stage', 'created_at']);
            $table->index(['hod_approval_status', 'created_at']);
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_access_requests', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['department_id']);
            $table->dropForeign(['hod_user_id']);
            $table->dropForeign(['divisional_director_user_id']);
            $table->dropForeign(['dict_user_id']);
            $table->dropForeign(['hod_it_user_id']);
            $table->dropForeign(['ict_officer_user_id']);
            
            // Drop indexes
            $table->dropIndex(['form_type', 'overall_status']);
            $table->dropIndex(['current_approval_stage', 'created_at']);
            $table->dropIndex(['hod_approval_status', 'created_at']);
            $table->dropIndex(['department_id']);
            
            // Drop columns
            $table->dropColumn([
                'pf_number',
                'staff_name',
                'phone_number',
                'department_id',
                'signature_path',
                'form_type',
                'services_requested',
                'hod_approval_status',
                'hod_comments',
                'hod_signature_path',
                'hod_approved_at',
                'hod_user_id',
                'divisional_director_approval_status',
                'divisional_director_comments',
                'divisional_director_signature_path',
                'divisional_director_approved_at',
                'divisional_director_user_id',
                'dict_approval_status',
                'dict_comments',
                'dict_signature_path',
                'dict_approved_at',
                'dict_user_id',
                'hod_it_approval_status',
                'hod_it_comments',
                'hod_it_signature_path',
                'hod_it_approved_at',
                'hod_it_user_id',
                'ict_officer_approval_status',
                'ict_officer_comments',
                'ict_officer_signature_path',
                'ict_officer_approved_at',
                'ict_officer_user_id',
                'overall_status',
                'current_approval_stage'
            ]);
        });
    }
};