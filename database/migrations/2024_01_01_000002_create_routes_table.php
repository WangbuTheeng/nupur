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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Route name like "Kathmandu - Pokhara"
            $table->string('source_city');
            $table->string('destination_city');
            $table->decimal('distance_km', 8, 2);
            $table->decimal('base_fare', 8, 2);
            $table->time('estimated_duration'); // HH:MM:SS format
            $table->json('stops')->nullable(); // Intermediate stops
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
