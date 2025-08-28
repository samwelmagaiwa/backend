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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique()->nullable();
            $table->text('description')->nullable();
            // Note: hod_user_id foreign key will be added later to avoid circular dependency
            $table->unsignedBigInteger('hod_user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes
            $table->index(['is_active', 'name']);
            $table->index('code');
            $table->index('hod_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};