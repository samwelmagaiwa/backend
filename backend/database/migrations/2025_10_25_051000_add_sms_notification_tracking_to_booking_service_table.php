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
            // SMS notification status tracking
            $table->json('sms_notifications')->nullable()->after('updated_at');
            
            // Timestamps for when SMS was sent to each approver
            $table->timestamp('sms_sent_to_hod_at')->nullable()->after('sms_notifications');
            $table->string('sms_to_hod_status')->default('pending')->after('sms_sent_to_hod_at'); // pending, sent, failed
            
            $table->timestamp('sms_sent_to_divisional_at')->nullable()->after('sms_to_hod_status');
            $table->string('sms_to_divisional_status')->default('pending')->after('sms_sent_to_divisional_at');
            
            $table->timestamp('sms_sent_to_ict_director_at')->nullable()->after('sms_to_divisional_status');
            $table->string('sms_to_ict_director_status')->default('pending')->after('sms_sent_to_ict_director_at');
            
            $table->timestamp('sms_sent_to_head_it_at')->nullable()->after('sms_to_ict_director_status');
            $table->string('sms_to_head_it_status')->default('pending')->after('sms_sent_to_head_it_at');
            
            // Track requester notification
            $table->timestamp('sms_sent_to_requester_at')->nullable()->after('sms_to_head_it_status');
            $table->string('sms_to_requester_status')->default('pending')->after('sms_sent_to_requester_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            $table->dropColumn([
                'sms_notifications',
                'sms_sent_to_hod_at',
                'sms_to_hod_status',
                'sms_sent_to_divisional_at',
                'sms_to_divisional_status',
                'sms_sent_to_ict_director_at',
                'sms_to_ict_director_status',
                'sms_sent_to_head_it_at',
                'sms_to_head_it_status',
                'sms_sent_to_requester_at',
                'sms_to_requester_status',
            ]);
        });
    }
};
