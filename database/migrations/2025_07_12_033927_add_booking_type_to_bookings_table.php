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
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_type', ['online', 'counter'])->default('online')->after('status');
            $table->string('payment_method')->nullable()->after('booking_type');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            $table->foreignId('booked_by')->nullable()->constrained('users')->onDelete('set null')->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['booked_by']);
            $table->dropColumn(['booking_type', 'payment_method', 'payment_status', 'booked_by']);
        });
    }
};
