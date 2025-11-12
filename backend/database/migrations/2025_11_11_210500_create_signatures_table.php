<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('signatures')) {
            Schema::create('signatures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('signature_hash', 256);
                $table->timestamp('signed_at')->nullable()->index();
                $table->timestamps();

                // Keep foreign keys lightweight to avoid coupling to a specific document table
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['document_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
