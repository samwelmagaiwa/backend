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
            // Indexes for notification count queries - optimized for each role
            
            // HOD pending requests: hod_status = 'pending' AND hod_approved_at IS NULL
            $table->index(['hod_status', 'hod_approved_at'], 'idx_hod_pending');
            
            // Divisional Director pending: hod_status = 'approved' AND divisional_status = 'pending' AND hod_approved_at IS NOT NULL AND divisional_approved_at IS NULL
            $table->index(['hod_status', 'divisional_status', 'hod_approved_at', 'divisional_approved_at'], 'idx_divisional_pending');
            
            // ICT Director pending: divisional_status = 'approved' AND ict_director_status = 'pending' AND divisional_approved_at IS NOT NULL AND ict_director_approved_at IS NULL
            $table->index(['divisional_status', 'ict_director_status', 'divisional_approved_at', 'ict_director_approved_at'], 'idx_ict_director_pending');
            
            // Head of IT pending: ict_director_status = 'approved' AND head_it_status = 'pending' AND ict_director_approved_at IS NOT NULL AND head_it_approved_at IS NULL
            $table->index(['ict_director_status', 'head_it_status', 'ict_director_approved_at', 'head_it_approved_at'], 'idx_head_it_pending');
            
            // ICT Officer available requests: head_it_status = 'approved' AND head_it_approved_at IS NOT NULL
            $table->index(['head_it_status', 'head_it_approved_at'], 'idx_ict_officer_available');
            
            // General status indexes for faster lookups
            $table->index('hod_status', 'idx_hod_status');
            $table->index('divisional_status', 'idx_divisional_status');
            $table->index('ict_director_status', 'idx_ict_director_status');
            $table->index('head_it_status', 'idx_head_it_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop the notification indexes
            $table->dropIndex('idx_hod_pending');
            $table->dropIndex('idx_divisional_pending');
            $table->dropIndex('idx_ict_director_pending');
            $table->dropIndex('idx_head_it_pending');
            $table->dropIndex('idx_ict_officer_available');
            $table->dropIndex('idx_hod_status');
            $table->dropIndex('idx_divisional_status');
            $table->dropIndex('idx_ict_director_status');
            $table->dropIndex('idx_head_it_status');
        });
    }
};
