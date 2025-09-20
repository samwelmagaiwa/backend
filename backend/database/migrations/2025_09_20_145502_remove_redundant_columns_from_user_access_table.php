<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove redundant columns from user_access table:
     * - wellsoft_modules (duplicate of wellsoft_modules_selected)
     * - jeeva_modules (duplicate of jeeva_modules_selected) 
     * - purpose (duplicate of internet_purposes)
     */
    public function up(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Remove redundant wellsoft_modules column (keep wellsoft_modules_selected)
            $table->dropColumn('wellsoft_modules');
            
            // Remove redundant jeeva_modules column (keep jeeva_modules_selected)
            $table->dropColumn('jeeva_modules');
            
            // Remove redundant purpose column (keep internet_purposes)
            $table->dropColumn('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Add back the removed columns
            $table->longText('wellsoft_modules')->nullable();
            $table->longText('jeeva_modules')->nullable();
            $table->longText('purpose')->nullable();
        });
    }
};
