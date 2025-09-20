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
            $table->text('hod_resubmission_notes')->nullable()->after('hod_comments');
            $table->timestamp('resubmitted_at')->nullable()->after('hod_resubmission_notes');
            $table->unsignedBigInteger('resubmitted_by')->nullable()->after('resubmitted_at');
            
            // Add foreign key constraint for resubmitted_by
            $table->foreign('resubmitted_by')->references('id')->on('users')->onDelete('set null');
            
            // Add index for better query performance
            $table->index(['resubmitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_access', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['resubmitted_by']);
            
            // Drop index
            $table->dropIndex(['resubmitted_at']);
            
            // Drop columns
            $table->dropColumn([
                'hod_resubmission_notes',
                'resubmitted_at',
                'resubmitted_by'
            ]);
        });
    }
};
