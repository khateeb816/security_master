<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = User::where('role', 'admin')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@patrolsync.com',
                'password' => Hash::make('123456'),
                'phone' => '+1234567890',
                'address' => '123 Admin Street',
                'city' => 'Admin City',
                'state' => 'Admin State',
                'zip' => '12345',
                'country' => 'Admin Country',
                'language' => 'en',
                'cnic' => 'ADMIN123456789',
                'notes' => 'Default administrator account',
                'nfc_uid' => null,
                'role' => 'admin',
                'status' => 'active',
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@patrolsync.com');
            $this->command->info('Password: 123456');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
