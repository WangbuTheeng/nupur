<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign roles to existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Assigning roles to existing users...');

        // Assign admin role
        $admin = User::where('email', 'admin@bookngo.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
            $this->info('Admin role assigned to admin@bookngo.com');
        }

        // Assign user roles to all users with role 'user'
        $users = User::where('role', 'user')->get();
        foreach ($users as $user) {
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
                $this->info("User role assigned to {$user->email}");
            }
        }

        $this->info('Role assignment completed!');
    }
}
