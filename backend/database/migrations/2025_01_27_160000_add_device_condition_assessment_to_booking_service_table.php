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
        if (Schema::hasTable('booking_service')) {
            Schema::table('booking_service', function (Blueprint $table) {
            // Device condition assessment fields
            $table->json('device_condition_receiving')->nullable()->after('return_status')
                  ->comment('Device condition assessment when receiving device back from borrower');
            
            $table->json('device_condition_issuing')->nullable()->after('device_condition_receiving')
                  ->comment('Device condition assessment when issuing device to borrower');
            
            $table->timestamp('device_received_at')->nullable()->after('device_condition_issuing')
                  ->comment('Timestamp when device was received back from borrower');
            
            $table->timestamp('device_issued_at')->nullable()->after('device_received_at')
                  ->comment('Timestamp when device was issued to borrower');
            
            $table->unsignedBigInteger('assessed_by')->nullable()->after('device_issued_at')
                  ->comment('ICT officer who performed the assessment');
            
            $table->text('assessment_notes')->nullable()->after('assessed_by')
                  ->comment('Additional notes from ICT officer during assessment');
            
            // Add foreign key constraint - only if users table exists
            if (Schema::hasTable('users')) {
                $table->foreign('assessed_by')->references('id')->on('users')->onDelete('set null');
            }
            });
        } else {
            echo "⚠️ Table booking_service does not exist yet. Migration will be skipped and handled later.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_service')) {
            Schema::table('booking_service', function (Blueprint $table) {
                if (Schema::hasColumn('booking_service', 'assessed_by')) {
                    $table->dropForeign(['assessed_by']);
                }
                $table->dropColumn([
                    'device_condition_receiving',
                    'device_condition_issuing', 
                    'device_received_at',
                    'device_issued_at',
                    'assessed_by',
                    'assessment_notes'
                ]);
            });
        }
    }
};