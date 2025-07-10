<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'bus_management',
            'schedule_management',
            'booking_management',
            'counter_booking',
            'revenue_reports',
            'seat_management',
            'user_management',
            'operator_management',
            'system_settings',
            'reports_analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign specific permissions to operator
        $operatorRole->syncPermissions([
            'bus_management',
            'schedule_management',
            'booking_management',
            'counter_booking',
            'revenue_reports',
            'seat_management',
        ]);

        // User role has no special permissions by default
    }
}
