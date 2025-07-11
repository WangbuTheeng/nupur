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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // booking_confirmed, payment_received, schedule_cancelled, etc.
            $table->morphs('notifiable'); // User who receives the notification
            $table->json('data'); // Notification data
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('channel', ['database', 'email', 'sms', 'push'])->default('database');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
