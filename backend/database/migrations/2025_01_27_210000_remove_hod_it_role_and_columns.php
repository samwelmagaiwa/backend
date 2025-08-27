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
        // Remove HOD_IT role from roles table
        DB::table('roles')->where('name', 'hod_it')->delete();
        
        // Remove HOD_IT role assignments from role_user table
        $hodItRoleId = DB::table('roles')->where('name', 'hod_it')->value('id');
        if ($hodItRoleId) {
            DB::table('role_user')->where('role_id', $hodItRoleId)->delete();
        }
        
        // Remove HOD_IT columns from module_access_requests table
        if (Schema::hasTable('module_access_requests')) {
            Schema::table('module_access_requests', function (Blueprint $table) {
                if (Schema::hasColumn('module_access_requests', 'hod_it_approval_status')) {
                    $table->dropColumn('hod_it_approval_status');
                }
                if (Schema::hasColumn('module_access_requests', 'hod_it_comments')) {
                    $table->dropColumn('hod_it_comments');
                }
                if (Schema::hasColumn('module_access_requests', 'hod_it_signature_path')) {
                    $table->dropColumn('hod_it_signature_path');
                }
                if (Schema::hasColumn('module_access_requests', 'hod_it_approved_at')) {
                    $table->dropColumn('hod_it_approved_at');
                }
                if (Schema::hasColumn('module_access_requests', 'hod_it_user_id')) {
                    $table->dropForeign(['hod_it_user_id']);
                    $table->dropColumn('hod_it_user_id');
                }
            });
        }
        
        // Update current_approval_stage enum to remove hod_it
        if (Schema::hasTable('module_access_requests')) {
            DB::statement("ALTER TABLE module_access_requests MODIFY COLUMN current_approval_stage ENUM('hod', 'divisional_director', 'dict', 'ict_officer', 'completed') DEFAULT 'hod'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate HOD_IT role
        DB::table('roles')->insert([
            'name' => 'hod_it',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Add back HOD_IT columns to module_access_requests table
        if (Schema::hasTable('module_access_requests')) {
            Schema::table('module_access_requests', function (Blueprint $table) {
                $table->enum('hod_it_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('dict_user_id');
                $table->text('hod_it_comments')->nullable()->after('hod_it_approval_status');
                $table->string('hod_it_signature_path')->nullable()->after('hod_it_comments');
                $table->timestamp('hod_it_approved_at')->nullable()->after('hod_it_signature_path');
                $table->foreignId('hod_it_user_id')->nullable()->constrained('users')->onDelete('set null')->after('hod_it_approved_at');
            });
        }
        
        // Update current_approval_stage enum to include hod_it
        if (Schema::hasTable('module_access_requests')) {
            DB::statement("ALTER TABLE module_access_requests MODIFY COLUMN current_approval_stage ENUM('hod', 'divisional_director', 'dict', 'hod_it', 'ict_officer', 'completed') DEFAULT 'hod'");
        }
    }
};