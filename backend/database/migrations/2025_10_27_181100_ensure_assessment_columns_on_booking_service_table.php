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
        if (!Schema::hasTable('booking_service')) {
            echo "⚠️ Table booking_service does not exist. Skipping ensure migration.\n";
            return;
        }

        Schema::table('booking_service', function (Blueprint $table) {
            // Receiving condition JSON
            if (!Schema::hasColumn('booking_service', 'device_condition_receiving')) {
                $table->json('device_condition_receiving')->nullable()->after('return_status')
                      ->comment('Device condition assessment when receiving device back from borrower');
            }

            // Issuing condition JSON
            if (!Schema::hasColumn('booking_service', 'device_condition_issuing')) {
                $table->json('device_condition_issuing')->nullable()->after('device_condition_receiving')
                      ->comment('Device condition assessment when issuing device to borrower');
            }

            // Received timestamp
            if (!Schema::hasColumn('booking_service', 'device_received_at')) {
                $table->timestamp('device_received_at')->nullable()->after('device_condition_issuing')
                      ->comment('Timestamp when device was received back from borrower');
            }

            // Issued timestamp
            if (!Schema::hasColumn('booking_service', 'device_issued_at')) {
                $table->timestamp('device_issued_at')->nullable()->after('device_received_at')
                      ->comment('Timestamp when device was issued to borrower');
            }

            // Assessed by FK
            if (!Schema::hasColumn('booking_service', 'assessed_by')) {
                $table->unsignedBigInteger('assessed_by')->nullable()->after('device_issued_at')
                      ->comment('ICT officer who performed the assessment');
                if (Schema::hasTable('users')) {
                    $table->foreign('assessed_by')->references('id')->on('users')->onDelete('set null');
                }
            }

            // Assessment notes
            if (!Schema::hasColumn('booking_service', 'assessment_notes')) {
                $table->text('assessment_notes')->nullable()->after('assessed_by')
                      ->comment('Additional notes from ICT officer during assessment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('booking_service')) {
            return;
        }

        Schema::table('booking_service', function (Blueprint $table) {
            if (Schema::hasColumn('booking_service', 'assessed_by')) {
                $table->dropForeign(['assessed_by']);
            }
            // Do not drop columns to avoid data loss in down(); keep it idempotent
        });
    }
};
