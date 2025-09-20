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
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('recipient_id')->after('id'); // User who receives the notification
            $table->unsignedBigInteger('sender_id')->nullable()->after('recipient_id'); // User who triggered the notification
            $table->unsignedBigInteger('access_request_id')->after('sender_id'); // Related access request
            $table->string('type', 50)->after('access_request_id'); // Type of notification (approval, rejection, etc.)
            $table->string('title')->after('type'); // Notification title
            $table->text('message')->after('title'); // Notification message
            $table->json('data')->nullable()->after('message'); // Additional data (JSON format)
            $table->timestamp('read_at')->nullable()->after('data'); // When notification was read
            
            // Foreign key constraints
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('access_request_id')->references('id')->on('user_access')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['recipient_id', 'read_at']);
            $table->index(['access_request_id']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['recipient_id']);
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['access_request_id']);
            
            // Drop indexes
            $table->dropIndex(['recipient_id', 'read_at']);
            $table->dropIndex(['access_request_id']);
            $table->dropIndex(['type']);
            
            // Drop columns
            $table->dropColumn([
                'recipient_id',
                'sender_id',
                'access_request_id',
                'type',
                'title',
                'message',
                'data',
                'read_at'
            ]);
        });
    }
};
