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
        Schema::create('bus_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // AC, Non-AC, Deluxe, VIP, etc.
            $table->text('description')->nullable();
            $table->integer('total_seats');
            $table->json('seat_layout'); // JSON structure for seat arrangement
            $table->decimal('base_fare_multiplier', 3, 2)->default(1.00); // Multiplier for base fare
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_types');
    }
};
