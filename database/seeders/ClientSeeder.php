<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'TechCorp Solutions',
                'email' => 'admin@techcorp.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0101',
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94105',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'TC001',
                'notes' => 'Technology company with multiple office locations',
                'role' => 'client',
                'status' => 'active',
            ],
            [
                'name' => 'Global Manufacturing Inc',
                'email' => 'admin@globalmfg.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0202',
                'address' => '456 Industrial Blvd',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip' => '60601',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'GM002',
                'notes' => 'Manufacturing company with warehouses',
                'role' => 'client',
                'status' => 'active',
            ],
            [
                'name' => 'SecureBank Financial',
                'email' => 'admin@securebank.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0303',
                'address' => '789 Finance Plaza',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'SB003',
                'notes' => 'Financial institution with high security requirements',
                'role' => 'client',
                'status' => 'active',
            ],
            [
                'name' => 'Retail Plus Stores',
                'email' => 'admin@retailplus.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0404',
                'address' => '321 Shopping Center',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90001',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'RP004',
                'notes' => 'Retail chain with multiple store locations',
                'role' => 'client',
                'status' => 'active',
            ],
            [
                'name' => 'Healthcare Systems Ltd',
                'email' => 'admin@healthcare.com',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0505',
                'address' => '654 Medical Center Dr',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02101',
                'country' => 'USA',
                'language' => 'en',
                'cnic' => 'HS005',
                'notes' => 'Healthcare provider with multiple facilities',
                'role' => 'client',
                'status' => 'active',
            ],
        ];

        foreach ($clients as $client) {
            User::create($client);
        }

        $this->command->info('Clients seeded successfully!');
    }
}
