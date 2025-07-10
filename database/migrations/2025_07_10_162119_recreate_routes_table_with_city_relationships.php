<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear dependent tables first
        DB::table('schedules')->truncate();
        DB::table('routes')->truncate();

        // Drop and recreate routes table with proper structure
        Schema::dropIfExists('routes');

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Route name like "Kathmandu - Pokhara"
            $table->foreignId('source_city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('destination_city_id')->constrained('cities')->onDelete('cascade');
            $table->decimal('distance_km', 8, 2);
            $table->decimal('base_fare', 8, 2);
            $table->time('estimated_duration'); // HH:MM:SS format
            $table->json('stops')->nullable(); // Intermediate stops
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['source_city_id', 'destination_city_id']);
            $table->index('is_active');
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop and recreate with old structure
        Schema::dropIfExists('routes');

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

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
