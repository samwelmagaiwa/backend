<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration makes the role_id column nullable in the users table
     * since the system now uses a many-to-many role system via the role_user table.
     * The role_id column is kept for backward compatibility but should default to NULL.
     * All users get 'staff' role by default through the many-to-many system.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make role_id nullable and set existing non-null values to null
            $table->unsignedBigInteger('role_id')->nullable()->default(null)->change();
        });
        
        // Set all existing role_id values to null since we use many-to-many now
        \DB::table('users')->update(['role_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert role_id to non-nullable
            // Note: This might fail if there are users with null role_id
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
        });
    }
};