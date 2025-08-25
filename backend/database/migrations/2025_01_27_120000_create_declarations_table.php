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
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('pf_number')->unique(); // Ensure PF number is unique
            $table->string('department');
            $table->string('job_title');
            $table->date('signature_date');
            $table->boolean('agreement_accepted')->default(false);
            $table->json('signature_info')->nullable(); // Store signature file information
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            // Add index for better performance
            $table->index(['user_id', 'pf_number']);
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};