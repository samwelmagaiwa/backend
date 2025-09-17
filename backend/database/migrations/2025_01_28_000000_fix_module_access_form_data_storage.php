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
            // Add module requested for field (Use / Revoke)
            if (!Schema::hasColumn('user_access', 'module_requested_for')) {
                $table->enum('module_requested_for', ['use', 'revoke'])->default('use')->after('access_type');
            }
            
            // Ensure all module fields exist with proper JSON casting
            if (!Schema::hasColumn('user_access', 'wellsoft_modules_selected')) {
                $table->json('wellsoft_modules_selected')->nullable()->comment('Selected Wellsoft modules')->after('wellsoft_modules');
            }
            
            if (!Schema::hasColumn('user_access', 'jeeva_modules_selected')) {
                $table->json('jeeva_modules_selected')->nullable()->comment('Selected Jeeva modules')->after('jeeva_modules');
            }
            
            // Add indexes for better query performance
            $table->index(['status', 'department_id'], 'idx_status_department');
            $table->index(['created_at'], 'idx_created_at');
        });
        
        echo "✅ Added module access form data storage fields to user_access table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn([
                'module_requested_for',
                'wellsoft_modules_selected',
                'jeeva_modules_selected'
            ]);
            
            $table->dropIndex('idx_status_department');
            $table->dropIndex('idx_created_at');
        });
        
        echo "✅ Removed module access form data storage fields from user_access table\n";
    }
};
