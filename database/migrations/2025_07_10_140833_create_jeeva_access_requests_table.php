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
        Schema::create('jeeva_access_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pf_number');
            $table->string('staff_name');
            $table->string('signature');
            $table->enum('module_action', ['use', 'revoke']);
            $table->json('modules');
            $table->text('access_rights')->nullable();
            $table->enum('access_duration', ['permanent', 'temporary']);
            $table->date('temporary_until')->nullable();
            // Approval
            $table->string('hodbm_name')->nullable();
            $table->string('hodbm_signature')->nullable();
            $table->date('hodbm_date')->nullable();
            $table->string('divdir_name')->nullable();
            $table->string('divdir_signature')->nullable();
            $table->date('divdir_date')->nullable();
            $table->string('ictdir_name')->nullable();
            $table->string('ictdir_signature_date')->nullable();
            $table->text('ictdir_comments')->nullable();
            // Implementation
            $table->string('headit_name')->nullable();
            $table->string('headit_signature_date')->nullable();
            $table->string('ict_officer_name')->nullable();
            $table->string('ict_officer_signature_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jeeva_access_requests');
    }
};
