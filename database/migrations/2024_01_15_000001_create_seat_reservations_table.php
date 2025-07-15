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
        Schema::create('seat_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->json('seat_numbers'); // Array of reserved seat numbers
            $table->enum('status', ['active', 'expired', 'converted_to_booking'])->default('active');
            $table->timestamp('expires_at');
            $table->timestamp('notified_at')->nullable(); // When expiry notification was sent
            $table->timestamps();

            // Indexes for performance
            $table->index(['schedule_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['expires_at', 'status']);
            
            // Unique constraint to prevent duplicate reservations
            $table->unique(['user_id', 'schedule_id'], 'unique_user_schedule_reservation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_reservations');
    }
};
