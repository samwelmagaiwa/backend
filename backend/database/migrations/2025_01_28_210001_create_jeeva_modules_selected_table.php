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
        Schema::create('jeeva_modules_selected', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_access_id')->constrained('user_access')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('jeeva_modules')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination of user_access_id and module_id
            $table->unique(['user_access_id', 'module_id']);
            
            // Add indexes for better performance
            $table->index('user_access_id');
            $table->index('module_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jeeva_modules_selected');
    }
};