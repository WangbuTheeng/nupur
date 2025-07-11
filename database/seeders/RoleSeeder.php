<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create permissions
        $permissions = [
            // Admin permissions
            'manage_users',
            'manage_operators',
            'manage_routes',
            'manage_cities',
            'manage_bus_types',
            'view_analytics',
            'manage_system_settings',

            // Operator permissions
            'manage_own_buses',
            'manage_own_schedules',
            'manage_own_bookings',
            'view_own_analytics',

            // Customer permissions
            'search_buses',
            'make_bookings',
            'view_own_bookings',
            'cancel_own_bookings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo([
            'manage_users',
            'manage_operators',
            'manage_routes',
            'manage_cities',
            'manage_bus_types',
            'view_analytics',
            'manage_system_settings',
        ]);

        $operatorRole->givePermissionTo([
            'manage_own_buses',
            'manage_own_schedules',
            'manage_own_bookings',
            'view_own_analytics',
        ]);

        $customerRole->givePermissionTo([
            'search_buses',
            'make_bookings',
            'view_own_bookings',
            'cancel_own_bookings',
        ]);
    }
}
