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
        Schema::create('user_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('pf_number');
            $table->string('staff_name');
            $table->string('phone_number');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('signature_path')->nullable();
            $table->text('purpose')->nullable();
            $table->json('request_type'); // changed from enum
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_review'])->default('pending');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['pf_number']);
            $table->index(['status', 'created_at']);
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access');
    }
};