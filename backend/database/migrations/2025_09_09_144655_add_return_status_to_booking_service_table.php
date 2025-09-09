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
            $table->enum('return_status', ['not_yet_returned', 'returned', 'returned_but_compromised'])
                  ->default('not_yet_returned')
                  ->after('device_returned_at')
                  ->comment('Status of device return: not_yet_returned, returned, returned_but_compromised');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_service', function (Blueprint $table) {
            $table->dropColumn('return_status');
        });
    }
};
