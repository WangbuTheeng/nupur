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
            // Add missing company fields
            $table->string('company_phone')->nullable()->after('company_address');
            $table->string('company_email')->nullable()->after('company_phone');
            $table->string('license_number')->nullable()->after('company_email');
            
            // Add settings field for operator preferences
            $table->json('settings')->nullable()->after('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_phone',
                'company_email', 
                'license_number',
                'settings'
            ]);
        });
    }
};
