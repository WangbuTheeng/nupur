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
        $admin = User::firstOrCreate(
            ['email' => 'admin@bookngo.com'],
            [
                'name' => 'Admin User',
                'phone' => '+977-9841234567',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create test regular user
        $user = User::firstOrCreate(
            ['email' => 'user@bookngo.com'],
            [
                'name' => 'Test User',
                'phone' => '+977-9851234567',
                'password' => Hash::make('user123'),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('customer')) {
            $user->assignRole('customer');
        }

        // Create sample users
        $ram = User::firstOrCreate(
            ['email' => 'ram@example.com'],
            [
                'name' => 'Ram Sharma',
                'phone' => '+977-9861234567',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$ram->hasRole('customer')) {
            $ram->assignRole('customer');
        }

        $sita = User::firstOrCreate(
            ['email' => 'sita@example.com'],
            [
                'name' => 'Sita Poudel',
                'phone' => '+977-9871234567',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$sita->hasRole('customer')) {
            $sita->assignRole('customer');
        }

        // Create test operator
        $operator = User::firstOrCreate(
            ['email' => 'operator@ktmexpress.com'],
            [
                'name' => 'Kathmandu Express',
                'phone' => '+977-9881234567',
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'company_name' => 'Kathmandu Express Pvt. Ltd.',
                'company_address' => 'New Baneshwor, Kathmandu',
                'company_license' => 'KTM-EXP-2024-001',
                'contact_person' => 'Rajesh Shrestha',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$operator->hasRole('operator')) {
            $operator->assignRole('operator');
        }
    }
}
