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
        Schema::table('user_access', function (Blueprint $table) {
            $table->text('ict_director_rejection_reasons')->nullable()->after('ict_director_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn('ict_director_rejection_reasons');
        });
    }
};
