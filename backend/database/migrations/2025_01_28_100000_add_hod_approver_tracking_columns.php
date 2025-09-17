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
            // Add HOD approver tracking columns
            if (!Schema::hasColumn('user_access', 'hod_approved_by')) {
                $table->unsignedBigInteger('hod_approved_by')->nullable()->after('hod_approved_at');
            }
            if (!Schema::hasColumn('user_access', 'hod_approved_by_name')) {
                $table->string('hod_approved_by_name')->nullable()->after('hod_approved_by');
            }
        });
        
        echo "✅ Added HOD approver tracking columns to user_access table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn(['hod_approved_by', 'hod_approved_by_name']);
        });
        
        echo "✅ Removed HOD approver tracking columns from user_access table\n";
    }
};
