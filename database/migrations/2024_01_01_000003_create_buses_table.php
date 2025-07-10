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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number')->unique();
            $table->string('operator_name');
            $table->foreignId('bus_type_id')->constrained()->onDelete('cascade');
            $table->string('license_plate')->unique();
            $table->year('manufacture_year');
            $table->integer('total_seats');
            $table->json('amenities')->nullable(); // WiFi, AC, TV, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
