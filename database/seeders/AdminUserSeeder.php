<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@bookngo.com',
            'phone' => '+977-9841234567',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        // Create test regular user
        User::create([
            'name' => 'Test User',
            'email' => 'user@bookngo.com',
            'phone' => '+977-9851234567',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        // Create sample users
        User::create([
            'name' => 'Ram Sharma',
            'email' => 'ram@example.com',
            'phone' => '+977-9861234567',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Sita Poudel',
            'email' => 'sita@example.com',
            'phone' => '+977-9871234567',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
    }
}
