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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message');
            $table->string('type')->default('notification'); // approval, notification, bulk, etc.
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->json('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('phone_number');
            $table->index('status');
            $table->index('type');
            $table->index(['user_id', 'created_at']);
            $table->index(['reference_id', 'reference_type']);
            $table->index('sent_at');
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
