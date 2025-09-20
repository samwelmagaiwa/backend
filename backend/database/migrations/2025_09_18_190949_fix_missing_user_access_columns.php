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
            // Add missing columns that are referenced in the model and controller
            if (!Schema::hasColumn('user_access', 'wellsoft_modules_selected')) {
                $table->json('wellsoft_modules_selected')->nullable()->after('wellsoft_modules');
            }
            if (!Schema::hasColumn('user_access', 'jeeva_modules_selected')) {
                $table->json('jeeva_modules_selected')->nullable()->after('jeeva_modules');
            }
            if (!Schema::hasColumn('user_access', 'module_requested_for')) {
                $table->enum('module_requested_for', ['use', 'revoke'])->default('use')->after('internet_purposes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn([
                'wellsoft_modules_selected',
                'jeeva_modules_selected', 
                'module_requested_for'
            ]);
        });
    }
};
