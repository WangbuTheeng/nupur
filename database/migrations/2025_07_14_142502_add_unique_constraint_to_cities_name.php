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
        // First, remove any duplicate cities by keeping only the first occurrence of each name
        $duplicates = DB::table('cities')
            ->select('name', DB::raw('MIN(id) as min_id'))
            ->groupBy('name')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Delete all records with the same name except the one with minimum ID
            DB::table('cities')
                ->where('name', $duplicate->name)
                ->where('id', '!=', $duplicate->min_id)
                ->delete();
        }

        // Now add unique constraint to prevent future duplicates
        Schema::table('cities', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
