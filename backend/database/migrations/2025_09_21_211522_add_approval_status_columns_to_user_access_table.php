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
            // Add approval status columns for each approval stage
            $table->string('hod_status')->nullable()->after('divisional_status')
                  ->comment('HOD approval status (pending, approved, rejected)');
            
            $table->string('ict_director_status')->nullable()->after('hod_status')
                  ->comment('ICT Director approval status (pending, approved, rejected)');
            
            $table->string('head_it_status')->nullable()->after('ict_director_status')
                  ->comment('Head IT approval status (pending, approved, rejected)');
            
            $table->string('ict_officer_status')->nullable()->after('head_it_status')
                  ->comment('ICT Officer implementation status (pending, implemented, rejected)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropColumn([
                'hod_status',
                'ict_director_status', 
                'head_it_status',
                'ict_officer_status'
            ]);
        });
    }
};
