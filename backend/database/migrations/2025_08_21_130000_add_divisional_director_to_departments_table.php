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
        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('divisional_director_id')->nullable()->after('hod_user_id');
            $table->boolean('has_divisional_director')->default(false)->after('divisional_director_id');
            
            // Add indexes
            $table->index('divisional_director_id');
            $table->index('has_divisional_director');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['divisional_director_id']);
            $table->dropIndex(['has_divisional_director']);
            $table->dropColumn(['divisional_director_id', 'has_divisional_director']);
        });
    }
};