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
        Schema::table('user_access', function (Blueprint $table) {
            // Module access fields
            if (!Schema::hasColumn('user_access', 'wellsoft_modules')) {
                $table->json('wellsoft_modules')->nullable()->after('purpose');
            }
            if (!Schema::hasColumn('user_access', 'jeeva_modules')) {
                $table->json('jeeva_modules')->nullable()->after('wellsoft_modules');
            }
            if (!Schema::hasColumn('user_access', 'internet_purposes')) {
                $table->json('internet_purposes')->nullable()->after('jeeva_modules');
            }
            
            // Access rights
            if (!Schema::hasColumn('user_access', 'access_type')) {
                $table->enum('access_type', ['permanent', 'temporary'])->default('permanent')->after('internet_purposes');
            }
            if (!Schema::hasColumn('user_access', 'temporary_until')) {
                $table->date('temporary_until')->nullable()->after('access_type');
            }
            
            // Divisional Director approval
            if (!Schema::hasColumn('user_access', 'divisional_director_name')) {
                $table->string('divisional_director_name')->nullable()->after('hod_approved_at');
            }
            if (!Schema::hasColumn('user_access', 'divisional_director_signature_path')) {
                $table->string('divisional_director_signature_path', 500)->nullable()->after('divisional_director_name');
            }
            if (!Schema::hasColumn('user_access', 'divisional_director_comments')) {
                $table->text('divisional_director_comments')->nullable()->after('divisional_director_signature_path');
            }
            if (!Schema::hasColumn('user_access', 'divisional_approved_at')) {
                $table->timestamp('divisional_approved_at')->nullable()->after('divisional_director_comments');
            }
            
            // ICT Director approval
            if (!Schema::hasColumn('user_access', 'ict_director_name')) {
                $table->string('ict_director_name')->nullable()->after('divisional_approved_at');
            }
            if (!Schema::hasColumn('user_access', 'ict_director_signature_path')) {
                $table->string('ict_director_signature_path', 500)->nullable()->after('ict_director_name');
            }
            if (!Schema::hasColumn('user_access', 'ict_director_comments')) {
                $table->text('ict_director_comments')->nullable()->after('ict_director_signature_path');
            }
            if (!Schema::hasColumn('user_access', 'ict_director_approved_at')) {
                $table->timestamp('ict_director_approved_at')->nullable()->after('ict_director_comments');
            }
            
            // Head of IT approval
            if (!Schema::hasColumn('user_access', 'head_it_name')) {
                $table->string('head_it_name')->nullable()->after('ict_director_approved_at');
            }
            if (!Schema::hasColumn('user_access', 'head_it_signature_path')) {
                $table->string('head_it_signature_path', 500)->nullable()->after('head_it_name');
            }
            if (!Schema::hasColumn('user_access', 'head_it_comments')) {
                $table->text('head_it_comments')->nullable()->after('head_it_signature_path');
            }
            if (!Schema::hasColumn('user_access', 'head_it_approved_at')) {
                $table->timestamp('head_it_approved_at')->nullable()->after('head_it_comments');
            }
            
            // ICT Officer implementation
            if (!Schema::hasColumn('user_access', 'ict_officer_name')) {
                $table->string('ict_officer_name')->nullable()->after('head_it_approved_at');
            }
            if (!Schema::hasColumn('user_access', 'ict_officer_signature_path')) {
                $table->string('ict_officer_signature_path', 500)->nullable()->after('ict_officer_name');
            }
            if (!Schema::hasColumn('user_access', 'ict_officer_comments')) {
                $table->text('ict_officer_comments')->nullable()->after('ict_officer_signature_path');
            }
            if (!Schema::hasColumn('user_access', 'ict_officer_implemented_at')) {
                $table->timestamp('ict_officer_implemented_at')->nullable()->after('ict_officer_comments');
            }
            
            // General implementation comments
            if (!Schema::hasColumn('user_access', 'implementation_comments')) {
                $table->text('implementation_comments')->nullable()->after('ict_officer_implemented_at');
            }
        });
        
        // Update status enum to include all workflow statuses
        try {
            DB::statement("ALTER TABLE user_access MODIFY COLUMN status ENUM(
                'pending', 
                'pending_hod', 
                'hod_approved', 
                'hod_rejected',
                'pending_divisional',
                'divisional_approved',
                'divisional_rejected',
                'pending_ict_director',
                'ict_director_approved', 
                'ict_director_rejected',
                'pending_head_it',
                'head_it_approved',
                'head_it_rejected',
                'pending_ict_officer',
                'implemented',
                'approved', 
                'rejected', 
                'in_review',
                'cancelled'
            ) DEFAULT 'pending'");
            
            echo "✅ Updated status enum with complete approval workflow\n";
        } catch (\Exception $e) {
            echo "⚠️ Status enum update failed: " . $e->getMessage() . "\n";
        }
        
        echo "✅ Added complete approval workflow columns to user_access table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn([
                'wellsoft_modules',
                'jeeva_modules', 
                'internet_purposes',
                'access_type',
                'temporary_until',
                'divisional_director_name',
                'divisional_director_signature_path',
                'divisional_director_comments',
                'divisional_approved_at',
                'ict_director_name',
                'ict_director_signature_path', 
                'ict_director_comments',
                'ict_director_approved_at',
                'head_it_name',
                'head_it_signature_path',
                'head_it_comments', 
                'head_it_approved_at',
                'ict_officer_name',
                'ict_officer_signature_path',
                'ict_officer_comments',
                'ict_officer_implemented_at',
                'implementation_comments'
            ]);
        });
        
        // Reset status enum to simpler version
        try {
            DB::statement("ALTER TABLE user_access MODIFY COLUMN status ENUM(
                'pending', 
                'pending_hod', 
                'hod_approved', 
                'hod_rejected',
                'approved', 
                'rejected', 
                'in_review',
                'cancelled'
            ) DEFAULT 'pending'");
        } catch (\Exception $e) {
            echo "⚠️ Status enum rollback failed: " . $e->getMessage() . "\n";
        }
        
        echo "✅ Removed approval workflow columns from user_access table\n";
    }
};
