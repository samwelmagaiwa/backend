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
            // Add device_inventory_id column after device_type
            $table->unsignedBigInteger('device_inventory_id')->nullable()->after('device_type');
            
            // Add foreign key constraint to device_inventory table
            $table->foreign('device_inventory_id')
                  ->references('id')
                  ->on('device_inventory')
                  ->onDelete('set null')
                  ->name('fk_booking_service_device_inventory');
                  
            // Add return_date_time column if it doesn't exist
            if (!Schema::hasColumn('booking_service', 'return_date_time')) {
                $table->dateTime('return_date_time')->nullable()->after('return_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign('fk_booking_service_device_inventory');
            
            // Drop the device_inventory_id column
            $table->dropColumn('device_inventory_id');
            
            // Drop return_date_time column if it was added by this migration
            if (Schema::hasColumn('booking_service', 'return_date_time')) {
                $table->dropColumn('return_date_time');
            }
        });
    }
};
