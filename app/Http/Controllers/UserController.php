<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with(['client', 'client.branches'])->latest()->paginate(10);
        $clients = \App\Models\Client::with('branches')->orderBy('name')->get();
        return view('users_fixed', compact('users', 'clients'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            
            $user = User::create($validated);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'redirect' => route('users.index'),
                    'user' => $user
                ]);
            }
            
            return redirect()->route('users.index')
                ->with('success', 'User created successfully');
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating user: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user->load('client')
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:15|unique:users,cnic,' . $user->id,
            'nfc_uid' => 'nullable|string|max:50|unique:users,nfc_uid,' . $user->id,
            'designation' => 'nullable|string|max:255',
            'role' => 'required|in:admin,manager,guard,user',
            'status' => 'required|in:active,inactive',
            'client_id' => 'required|exists:clients,id',
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(function ($query) use ($request) {
                    return $query->where('client_id', $request->client_id);
                })
            ],
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // If password is provided, hash it
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            // Remove password from the array if not provided
            unset($validated['password']);
        }

        // If branch is not provided but coordinates are, try to find a matching branch
        if (empty($validated['branch_id']) && !empty($validated['latitude']) && !empty($validated['longitude'])) {
            $matchingBranch = \App\Models\Branch::where('client_id', $validated['client_id'])
                ->where('latitude', $validated['latitude'])
                ->where('longitude', $validated['longitude'])
                ->first();
                
            if ($matchingBranch) {
                $validated['branch_id'] = $matchingBranch->id;
            }
        }

        // Update the user
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user->load(['client', 'branch'])
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        try {
            $user->delete();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully',
                    'redirect' => route('users.index')
                ]);
            }
            
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully');
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting user: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
    
    /**
     * Update only the branch ID for a user
     */
    public function updateBranch(Request $request, User $user)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);
        
        $user->update([
            'branch_id' => $validated['branch_id']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User branch updated successfully',
            'user' => $user->load(['client', 'branch'])
        ]);
    }
    
    /**
     * Get user details for editing.
     */
    public function getUser(User $user)
    {
        $user->load(['client', 'branch', 'client.branches']);
        $matchingBranch = $user->findMatchingBranch();
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'matching_branch' => $matchingBranch,
            'branch_id' => $user->branch_id // Explicitly include branch_id
        ]);
    }
}
