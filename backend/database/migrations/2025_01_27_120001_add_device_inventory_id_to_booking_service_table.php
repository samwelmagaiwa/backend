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
                if (!Schema::hasColumn('booking_service', 'device_inventory_id')) {
                    $table->unsignedBigInteger('device_inventory_id')->nullable()->after('custom_device');
                    
                    if (Schema::hasTable('device_inventory')) {
                        $table->foreign('device_inventory_id')->references('id')->on('device_inventory')->onDelete('set null');
                    }
                    
                    $table->index('device_inventory_id');
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
        if (Schema::hasTable('booking_service') && Schema::hasColumn('booking_service', 'device_inventory_id')) {
            Schema::table('booking_service', function (Blueprint $table) {
                $table->dropForeign(['device_inventory_id']);
                $table->dropIndex(['device_inventory_id']);
                $table->dropColumn('device_inventory_id');
            });
        }
    }
};