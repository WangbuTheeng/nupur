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
        // Update the schedules table status enum to include 'completed'
        DB::statement("ALTER TABLE schedules MODIFY COLUMN status ENUM('scheduled', 'boarding', 'departed', 'arrived', 'completed', 'cancelled') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any 'completed' status to 'arrived' to avoid data loss
        DB::table('schedules')
            ->where('status', 'completed')
            ->update(['status' => 'arrived']);
            
        // Revert back to original enum without 'completed'
        DB::statement("ALTER TABLE schedules MODIFY COLUMN status ENUM('scheduled', 'boarding', 'departed', 'arrived', 'cancelled') DEFAULT 'scheduled'");
    }
};
