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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('booking_id')->constrained()->onDelete('cascade');
            $table->string('gateway_transaction_id')->nullable()->after('transaction_id');
            $table->string('currency', 3)->default('NRs')->after('amount');
            $table->json('gateway_data')->nullable()->after('gateway_response');
            $table->timestamp('failed_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'gateway_transaction_id', 'currency', 'gateway_data', 'failed_at']);
        });
    }
};
