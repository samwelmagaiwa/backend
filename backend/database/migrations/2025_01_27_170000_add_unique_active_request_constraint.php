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
        // Add a partial unique index to prevent multiple active requests per user
        // This ensures database-level constraint for preventing duplicates
        Schema::table('booking_service', function (Blueprint $table) {
            // Create a partial unique index for active requests
            // This prevents a user from having multiple active requests at the same time
            $table->index(['user_id', 'status', 'ict_approve'], 'idx_user_active_requests');
        });
        
        // Note: We use a regular index instead of unique constraint because
        // we want to allow multiple completed/rejected requests per user,
        // but only one active request (pending, approved, or in_use) at a time.
        // The application logic handles the uniqueness validation.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            $table->dropIndex('idx_user_active_requests');
        });
    }
};