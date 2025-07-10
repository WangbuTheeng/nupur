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
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bookngo.com',
            'phone' => '+977-9841234567',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $admin->assignRole('admin');

        // Create test regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@bookngo.com',
            'phone' => '+977-9851234567',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $user->assignRole('user');

        // Create sample users
        $ram = User::create([
            'name' => 'Ram Sharma',
            'email' => 'ram@example.com',
            'phone' => '+977-9861234567',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $ram->assignRole('user');

        $sita = User::create([
            'name' => 'Sita Poudel',
            'email' => 'sita@example.com',
            'phone' => '+977-9871234567',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $sita->assignRole('user');

        // Create test operator
        $operator = User::create([
            'name' => 'Kathmandu Express',
            'email' => 'operator@ktmexpress.com',
            'phone' => '+977-9881234567',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'company_name' => 'Kathmandu Express Pvt. Ltd.',
            'company_address' => 'New Baneshwor, Kathmandu',
            'company_license' => 'KTM-EXP-2024-001',
            'contact_person' => 'Rajesh Shrestha',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $operator->assignRole('operator');
    }
}
