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
        Schema::create('ict_task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_access_id')->constrained('user_access')->onDelete('cascade');
            $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade'); // Head of IT
            $table->foreignId('ict_officer_user_id')->constrained('users')->onDelete('cascade'); // ICT Officer
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->text('assignment_notes')->nullable();
            $table->text('progress_notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamp('assigned_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['ict_officer_user_id', 'status']);
            $table->index(['assigned_by_user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ict_task_assignments');
    }
};
