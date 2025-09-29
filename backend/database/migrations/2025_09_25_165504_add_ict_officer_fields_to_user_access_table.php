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
            // Add missing ICT Officer workflow fields
            if (!Schema::hasColumn('user_access', 'ict_officer_user_id')) {
                $table->unsignedBigInteger('ict_officer_user_id')->nullable()->after('ict_officer_status');
                $table->foreign('ict_officer_user_id')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('user_access', 'ict_officer_assigned_at')) {
                $table->timestamp('ict_officer_assigned_at')->nullable()->after('ict_officer_user_id');
            }
            
            if (!Schema::hasColumn('user_access', 'ict_officer_started_at')) {
                $table->timestamp('ict_officer_started_at')->nullable()->after('ict_officer_assigned_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop the foreign key and columns in reverse order
            if (Schema::hasColumn('user_access', 'ict_officer_user_id')) {
                $table->dropForeign(['ict_officer_user_id']);
            }
            
            $table->dropColumn([
                'ict_officer_started_at',
                'ict_officer_assigned_at', 
                'ict_officer_user_id'
            ]);
        });
    }
};
