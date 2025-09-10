<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->enum('assessment_type', ['issuing', 'receiving']);
            $table->string('physical_condition');
            $table->string('functionality');
            $table->boolean('accessories_complete')->default(false);
            $table->boolean('has_damage')->default(false);
            $table->text('damage_description')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();

            $table->index('booking_id');
            $table->index(['booking_id', 'assessment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_assessments');
    }
};
