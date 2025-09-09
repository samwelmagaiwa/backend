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
        Schema::table('booking_service', function (Blueprint $table) {
            // ICT Approval fields
            $table->enum('ict_approve', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->foreignId('ict_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('ict_approve');
            $table->timestamp('ict_approved_at')->nullable()->after('ict_approved_by');
            $table->text('ict_notes')->nullable()->after('ict_approved_at');
            
            // Add indexes for better performance
            $table->index(['ict_approve']);
            $table->index(['ict_approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            $table->dropForeign(['ict_approved_by']);
            $table->dropIndex(['ict_approve']);
            $table->dropIndex(['ict_approved_at']);
            $table->dropColumn(['ict_approve', 'ict_approved_by', 'ict_approved_at', 'ict_notes']);
        });
    }
};
