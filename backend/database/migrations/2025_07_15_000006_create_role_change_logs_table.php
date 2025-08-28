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
        Schema::create('role_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->enum('action', ['assigned', 'removed'])->default('assigned');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable(); // Additional context about the change
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['user_id', 'changed_at']);
            $table->index(['role_id', 'changed_at']);
            $table->index(['changed_by', 'changed_at']);
            $table->index(['action', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_change_logs');
    }
};