<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Get all branches for a client
     */
    public function index(Client $client)
    {
        try {
            $branches = $client->branches()->latest()->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $branches->items(),
                'pagination' => [
                    'current_page' => $branches->currentPage(),
                    'last_page' => $branches->lastPage(),
                    'per_page' => $branches->perPage(),
                    'total' => $branches->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load branches',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all branches for a client (for dropdowns)
     */
    public function getBranchesByClient(Client $client)
    {
        try {
            $branches = $client->branches()->select([
                'id',
                'branch_name',
                'manager_name',
                'email',
                'phone',
                'city',
                'country',
                'latitude',
                'longitude',
                'address',
                'state',
                'zip'
            ])->get();
            
            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load branches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'branch_name' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            DB::beginTransaction();
            
            $branch = Branch::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Branch created successfully',
                'data' => $branch
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified branch.
     */
    public function show(Client $client, Branch $branch)
    {
        return response()->json([
            'success' => true,
            'data' => $branch->load('client')
        ]);
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Client $client, Branch $branch)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            DB::beginTransaction();
            
            $branch->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully',
                'data' => $branch
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Client $client, Branch $branch)
    {
        try {
            DB::beginTransaction();
            
            $branch->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Branch deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
