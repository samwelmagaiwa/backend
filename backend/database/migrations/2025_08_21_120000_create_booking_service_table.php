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
        Schema::create('booking_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Booking Information
            $table->date('booking_date');
            $table->string('borrower_name');
            $table->enum('device_type', [
                'projector', 
                'tv_remote', 
                'hdmi_cable', 
                'monitor', 
                'cpu', 
                'keyboard', 
                'pc', 
                'others'
            ]);
            $table->string('custom_device')->nullable(); // For when device_type is 'others'
            $table->string('department');
            $table->string('phone_number');
            $table->date('return_date'); // Collection date in form is actually return date
            $table->time('return_time');
            $table->text('reason');
            
            // Signature
            $table->string('signature_path')->nullable();
            
            // Status tracking
            $table->enum('status', [
                'pending', 
                'approved', 
                'rejected', 
                'in_use', 
                'returned', 
                'overdue'
            ])->default('pending');
            
            // Admin fields
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('device_collected_at')->nullable();
            $table->timestamp('device_returned_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['booking_date']);
            $table->index(['return_date']);
            $table->index(['device_type']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_service');
    }
};