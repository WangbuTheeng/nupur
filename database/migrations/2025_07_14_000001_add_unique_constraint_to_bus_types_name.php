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
        Schema::table('bus_types', function (Blueprint $table) {
            // Add unique constraint to name column to prevent duplicates
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_types', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique(['name']);
        });
    }
};
