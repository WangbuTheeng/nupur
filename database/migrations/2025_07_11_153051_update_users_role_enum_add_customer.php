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
        Schema::table('users', function (Blueprint $table) {
            // Update role enum to include customer
            $table->enum('role', ['admin', 'operator', 'customer', 'user'])->default('customer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to previous enum
            $table->enum('role', ['admin', 'operator', 'user'])->default('user')->change();
        });
    }
};
