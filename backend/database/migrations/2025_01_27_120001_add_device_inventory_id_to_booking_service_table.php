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
            $table->unsignedBigInteger('device_inventory_id')->nullable()->after('custom_device');
            $table->foreign('device_inventory_id')->references('id')->on('device_inventory')->onDelete('set null');
            $table->index('device_inventory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            $table->dropForeign(['device_inventory_id']);
            $table->dropIndex(['device_inventory_id']);
            $table->dropColumn('device_inventory_id');
        });
    }
};