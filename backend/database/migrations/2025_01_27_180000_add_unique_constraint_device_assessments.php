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
        if (Schema::hasTable('device_assessments')) {
            Schema::table('device_assessments', function (Blueprint $table) {
                // Add unique constraint to prevent duplicate assessments
                // Only one assessment per booking per assessment type (issuing/receiving)
                $table->unique(['booking_id', 'assessment_type'], 'unique_booking_assessment_type');
            });
        } else {
            echo "⚠️ Table device_assessments does not exist yet. Migration will be skipped and handled later.\n";
        }
    }

    /**\n     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('device_assessments')) {
            Schema::table('device_assessments', function (Blueprint $table) {
                $table->dropUnique('unique_booking_assessment_type');
            });
        }
    }
};