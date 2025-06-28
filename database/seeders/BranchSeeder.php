<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\User;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all clients
        $clients = User::where('role', 'client')->get();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Please run ClientSeeder first.');
            return;
        }

        $branches = [
            // TechCorp Solutions branches
            [
                'client_name' => 'TechCorp Solutions',
                'name' => 'TechCorp HQ',
                'email' => 'hq@techcorp.com',
                'phone' => '+1-555-0101',
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94105',
                'country' => 'USA',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
            ],
            [
                'client_name' => 'TechCorp Solutions',
                'name' => 'TechCorp East Coast',
                'email' => 'east@techcorp.com',
                'phone' => '+1-555-0102',
                'address' => '456 Innovation Drive',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02101',
                'country' => 'USA',
                'latitude' => 42.3601,
                'longitude' => -71.0589,
            ],

            // Global Manufacturing branches
            [
                'client_name' => 'Global Manufacturing Inc',
                'name' => 'GM Chicago Plant',
                'email' => 'chicago@globalmfg.com',
                'phone' => '+1-555-0201',
                'address' => '456 Industrial Blvd',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip' => '60601',
                'country' => 'USA',
                'latitude' => 41.8781,
                'longitude' => -87.6298,
            ],
            [
                'client_name' => 'Global Manufacturing Inc',
                'name' => 'GM Detroit Warehouse',
                'email' => 'detroit@globalmfg.com',
                'phone' => '+1-555-0202',
                'address' => '789 Factory Lane',
                'city' => 'Detroit',
                'state' => 'MI',
                'zip' => '48201',
                'country' => 'USA',
                'latitude' => 42.3314,
                'longitude' => -83.0458,
            ],

            // SecureBank branches
            [
                'client_name' => 'SecureBank Financial',
                'name' => 'SecureBank Manhattan',
                'email' => 'manhattan@securebank.com',
                'phone' => '+1-555-0301',
                'address' => '789 Finance Plaza',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ],
            [
                'client_name' => 'SecureBank Financial',
                'name' => 'SecureBank Brooklyn',
                'email' => 'brooklyn@securebank.com',
                'phone' => '+1-555-0302',
                'address' => '321 Banking Street',
                'city' => 'Brooklyn',
                'state' => 'NY',
                'zip' => '11201',
                'country' => 'USA',
                'latitude' => 40.6782,
                'longitude' => -73.9442,
            ],

            // Retail Plus branches
            [
                'client_name' => 'Retail Plus Stores',
                'name' => 'Retail Plus Downtown LA',
                'email' => 'downtown@retailplus.com',
                'phone' => '+1-555-0401',
                'address' => '321 Shopping Center',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90001',
                'country' => 'USA',
                'latitude' => 34.0522,
                'longitude' => -118.2437,
            ],
            [
                'client_name' => 'Retail Plus Stores',
                'name' => 'Retail Plus Hollywood',
                'email' => 'hollywood@retailplus.com',
                'phone' => '+1-555-0402',
                'address' => '654 Star Boulevard',
                'city' => 'Hollywood',
                'state' => 'CA',
                'zip' => '90028',
                'country' => 'USA',
                'latitude' => 34.0928,
                'longitude' => -118.3287,
            ],
            [
                'client_name' => 'Retail Plus Stores',
                'name' => 'Retail Plus Santa Monica',
                'email' => 'santamonica@retailplus.com',
                'phone' => '+1-555-0403',
                'address' => '987 Beach Mall',
                'city' => 'Santa Monica',
                'state' => 'CA',
                'zip' => '90401',
                'country' => 'USA',
                'latitude' => 34.0195,
                'longitude' => -118.4912,
            ],

            // Healthcare Systems branches
            [
                'client_name' => 'Healthcare Systems Ltd',
                'name' => 'HSL Main Hospital',
                'email' => 'main@healthcare.com',
                'phone' => '+1-555-0501',
                'address' => '654 Medical Center Dr',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02101',
                'country' => 'USA',
                'latitude' => 42.3601,
                'longitude' => -71.0589,
            ],
            [
                'client_name' => 'Healthcare Systems Ltd',
                'name' => 'HSL Emergency Clinic',
                'email' => 'emergency@healthcare.com',
                'phone' => '+1-555-0502',
                'address' => '123 Emergency Way',
                'city' => 'Cambridge',
                'state' => 'MA',
                'zip' => '02139',
                'country' => 'USA',
                'latitude' => 42.3736,
                'longitude' => -71.1097,
            ],
        ];

        foreach ($branches as $branchData) {
            $client = $clients->where('name', $branchData['client_name'])->first();

            if ($client) {
                unset($branchData['client_name']); // Remove the client_name key
                $branchData['user_id'] = $client->id;

                Branch::create($branchData);
            }
        }

        $this->command->info('Branches seeded successfully!');
    }
}
