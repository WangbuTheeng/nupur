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

        // Clear existing data to avoid foreign key conflicts
        DB::table('bookings')->truncate();
        DB::table('schedules')->truncate();
        DB::table('buses')->truncate();

        Schema::table('buses', function (Blueprint $table) {
            // Add operator foreign key if it doesn't exist
            if (!Schema::hasColumn('buses', 'operator_id')) {
                $table->foreignId('operator_id')->after('bus_number')->constrained('users')->onDelete('cascade');
            }

            // Add seat layout JSON field if it doesn't exist
            if (!Schema::hasColumn('buses', 'seat_layout')) {
                $table->json('seat_layout')->after('total_seats')->nullable();
            }

            // Add more bus details if they don't exist
            if (!Schema::hasColumn('buses', 'model')) {
                $table->string('model')->after('license_plate')->nullable();
            }
            if (!Schema::hasColumn('buses', 'color')) {
                $table->string('color')->after('model')->nullable();
            }
            if (!Schema::hasColumn('buses', 'description')) {
                $table->text('description')->after('amenities')->nullable();
            }

            // Drop operator_name if it exists
            if (Schema::hasColumn('buses', 'operator_name')) {
                $table->dropColumn('operator_name');
            }

            // Add indexes if they don't exist
            if (!Schema::hasColumn('buses', 'operator_id') || !DB::select("SHOW INDEX FROM buses WHERE Key_name = 'buses_operator_id_index'")) {
                $table->index('operator_id');
            }
            if (!DB::select("SHOW INDEX FROM buses WHERE Key_name = 'buses_is_active_index'")) {
                $table->index('is_active');
            }
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            // Add back operator_name
            $table->string('operator_name')->after('bus_number');

            // Drop new columns
            $table->dropForeign(['operator_id']);
            $table->dropColumn(['operator_id', 'seat_layout', 'model', 'color', 'description']);

            // Drop indexes
            $table->dropIndex(['operator_id']);
            $table->dropIndex(['is_active']);
        });
    }
};
