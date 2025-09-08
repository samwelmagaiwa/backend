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
        Schema::create('device_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('device_code')->unique(); // Unique identifier for the device type
            $table->text('description')->nullable();
            $table->integer('total_quantity')->default(0); // Total devices available
            $table->integer('available_quantity')->default(0); // Currently available devices
            $table->integer('borrowed_quantity')->default(0); // Currently borrowed devices
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable(); // Admin who created this device
            $table->unsignedBigInteger('updated_by')->nullable(); // Admin who last updated this device
            $table->timestamps();

            // Add indexes
            $table->index(['is_active', 'device_name']);
            $table->index('device_code');
            $table->index('available_quantity');

            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_inventory');
    }
};