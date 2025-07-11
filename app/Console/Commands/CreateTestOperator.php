<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestOperator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-operator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test operator for development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if operator already exists
        $existingOperator = User::where('email', 'operator@ktmexpress.com')->first();
        if ($existingOperator) {
            $this->info('Test operator already exists.');
            return;
        }

        // Create operator
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

        $this->info('Test operator created successfully!');
        $this->info('Email: operator@ktmexpress.com');
        $this->info('Password: operator123');
    }
}
