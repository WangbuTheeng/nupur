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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->date('travel_date');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->decimal('fare', 8, 2);
            $table->integer('available_seats');
            $table->enum('status', ['scheduled', 'boarding', 'departed', 'arrived', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique schedule per bus per date
            $table->unique(['bus_id', 'travel_date', 'departure_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
