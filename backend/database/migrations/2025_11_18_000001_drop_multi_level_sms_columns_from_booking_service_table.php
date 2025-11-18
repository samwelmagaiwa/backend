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
            // Drop multi-level approver SMS tracking columns that are no longer used
            $table->dropColumn([
                'sms_sent_to_hod_at',
                'sms_to_hod_status',
                'sms_sent_to_divisional_at',
                'sms_to_divisional_status',
                'sms_sent_to_ict_director_at',
                'sms_to_ict_director_status',
                'sms_sent_to_head_it_at',
                'sms_to_head_it_status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            // Recreate the dropped columns with their original definitions
            $table->timestamp('sms_sent_to_hod_at')->nullable()->after('sms_notifications');
            $table->string('sms_to_hod_status')->default('pending')->after('sms_sent_to_hod_at');

            $table->timestamp('sms_sent_to_divisional_at')->nullable()->after('sms_to_hod_status');
            $table->string('sms_to_divisional_status')->default('pending')->after('sms_sent_to_divisional_at');

            $table->timestamp('sms_sent_to_ict_director_at')->nullable()->after('sms_to_divisional_status');
            $table->string('sms_to_ict_director_status')->default('pending')->after('sms_sent_to_ict_director_at');

            $table->timestamp('sms_sent_to_head_it_at')->nullable()->after('sms_to_ict_director_status');
            $table->string('sms_to_head_it_status')->default('pending')->after('sms_sent_to_head_it_at');
        });
    }
};
