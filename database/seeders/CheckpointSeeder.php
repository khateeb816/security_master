<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Checkpoint;
use App\Models\Branch;

class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all branches
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Please run BranchSeeder first.');
            return;
        }

        $checkpoints = [
            // TechCorp HQ checkpoints
            [
                'branch_name' => 'TechCorp HQ',
                'name' => 'Main Entrance',
                'description' => 'Primary security checkpoint at main entrance',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
                'radius' => 50,
                'is_active' => true,
            ],
            [
                'branch_name' => 'TechCorp HQ',
                'name' => 'Parking Garage',
                'description' => 'Security checkpoint for parking garage access',
                'latitude' => 37.7750,
                'longitude' => -122.4195,
                'radius' => 30,
                'is_active' => true,
            ],
            [
                'branch_name' => 'TechCorp HQ',
                'name' => 'Server Room',
                'description' => 'High-security checkpoint for server room access',
                'latitude' => 37.7748,
                'longitude' => -122.4193,
                'radius' => 20,
                'is_active' => true,
            ],

            // GM Chicago Plant checkpoints
            [
                'branch_name' => 'GM Chicago Plant',
                'name' => 'Factory Gate A',
                'description' => 'Main factory entrance checkpoint',
                'latitude' => 41.8781,
                'longitude' => -87.6298,
                'radius' => 40,
                'is_active' => true,
            ],
            [
                'branch_name' => 'GM Chicago Plant',
                'name' => 'Loading Dock',
                'description' => 'Security checkpoint for loading dock area',
                'latitude' => 41.8782,
                'longitude' => -87.6299,
                'radius' => 35,
                'is_active' => true,
            ],

            // SecureBank Manhattan checkpoints
            [
                'branch_name' => 'SecureBank Manhattan',
                'name' => 'Bank Lobby',
                'description' => 'Main bank lobby security checkpoint',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'radius' => 25,
                'is_active' => true,
            ],
            [
                'branch_name' => 'SecureBank Manhattan',
                'name' => 'Vault Access',
                'description' => 'High-security checkpoint for vault access',
                'latitude' => 40.7127,
                'longitude' => -74.0059,
                'radius' => 15,
                'is_active' => true,
            ],

            // Retail Plus Downtown LA checkpoints
            [
                'branch_name' => 'Retail Plus Downtown LA',
                'name' => 'Store Entrance',
                'description' => 'Main store entrance security checkpoint',
                'latitude' => 34.0522,
                'longitude' => -118.2437,
                'radius' => 30,
                'is_active' => true,
            ],
            [
                'branch_name' => 'Retail Plus Downtown LA',
                'name' => 'Back Office',
                'description' => 'Security checkpoint for back office area',
                'latitude' => 34.0523,
                'longitude' => -118.2438,
                'radius' => 20,
                'is_active' => true,
            ],

            // HSL Main Hospital checkpoints
            [
                'branch_name' => 'HSL Main Hospital',
                'name' => 'Emergency Entrance',
                'description' => 'Emergency department entrance checkpoint',
                'latitude' => 42.3601,
                'longitude' => -71.0589,
                'radius' => 40,
                'is_active' => true,
            ],
            [
                'branch_name' => 'HSL Main Hospital',
                'name' => 'ICU Access',
                'description' => 'Intensive care unit access checkpoint',
                'latitude' => 42.3600,
                'longitude' => -71.0588,
                'radius' => 25,
                'is_active' => true,
            ],
        ];

        foreach ($checkpoints as $checkpointData) {
            $branch = $branches->where('name', $checkpointData['branch_name'])->first();

            if ($branch) {
                unset($checkpointData['branch_name']); // Remove the branch_name key
                $checkpointData['branch_id'] = $branch->id;
                $checkpointData['client_id'] = $branch->user_id;

                Checkpoint::create($checkpointData);
            }
        }

        $this->command->info('Checkpoints seeded successfully!');
    }
}
