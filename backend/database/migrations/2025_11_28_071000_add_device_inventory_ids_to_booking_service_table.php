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
            if (!Schema::hasColumn('booking_service', 'device_inventory_ids')) {
                $table->json('device_inventory_ids')->nullable()->after('device_inventory_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            if (Schema::hasColumn('booking_service', 'device_inventory_ids')) {
                $table->dropColumn('device_inventory_ids');
            }
        });
    }
};
