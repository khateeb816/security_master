<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guards = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1001',
                'address' => '100 Guard Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94102',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS001',
                'notes' => 'Experienced security guard with 5 years experience',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:3F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1002',
                'address' => '200 Security Ave',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip' => '60602',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS002',
                'notes' => 'Former police officer, excellent attention to detail',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:4F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'David Johnson',
                'email' => 'david.johnson@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1003',
                'address' => '300 Patrol Road',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10002',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS003',
                'notes' => 'Night shift specialist, certified in first aid',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:5F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1004',
                'address' => '400 Watch Lane',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90002',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS004',
                'notes' => 'Customer service oriented, bilingual (English/Spanish)',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:6F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1005',
                'address' => '500 Safety Drive',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02102',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS005',
                'notes' => 'IT security background, familiar with surveillance systems',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:7F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'Lisa Davis',
                'email' => 'lisa.davis@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1006',
                'address' => '600 Protection Way',
                'city' => 'Miami',
                'state' => 'FL',
                'zip' => '33101',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS006',
                'notes' => 'Event security specialist, crowd control experience',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:8F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1007',
                'address' => '700 Guard Circle',
                'city' => 'Seattle',
                'state' => 'WA',
                'zip' => '98101',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS007',
                'notes' => 'Retired military, excellent leadership skills',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:9F',
                'role' => 'guard',
                'status' => 'active',
            ],
            [
                'name' => 'Jennifer Martinez',
                'email' => 'jennifer.martinez@security.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-1008',
                'address' => '800 Security Blvd',
                'city' => 'Denver',
                'state' => 'CO',
                'zip' => '80201',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GS008',
                'notes' => 'Certified in emergency response, CPR trained',
                'nfc_uid' => '04:5A:2B:8C:1D:2E:AF',
                'role' => 'guard',
                'status' => 'active',
            ],
        ];

        foreach ($guards as $guard) {
            User::create($guard);
        }

        $this->command->info('Guards seeded successfully!');
    }
}
