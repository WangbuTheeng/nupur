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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->onDelete('cascade');
            $table->string('seat_number'); // A1, A2, B1, B2, etc.
            $table->integer('row_number');
            $table->integer('column_number');
            $table->enum('seat_type', ['regular', 'vip', 'reserved', 'disabled'])->default('regular');
            $table->boolean('is_window')->default(false);
            $table->boolean('is_aisle')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            // Ensure unique seat per bus
            $table->unique(['bus_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
