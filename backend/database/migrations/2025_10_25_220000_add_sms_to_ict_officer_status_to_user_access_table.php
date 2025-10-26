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
            // SMS notification status tracking for ICT Officer task assignment
            $table->timestamp('sms_sent_to_ict_officer_at')->nullable()->after('sms_to_requester_status');
            $table->string('sms_to_ict_officer_status')->default('pending')->after('sms_sent_to_ict_officer_at'); // pending, sent, failed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn([
                'sms_sent_to_ict_officer_at',
                'sms_to_ict_officer_status',
            ]);
        });
    }
};
