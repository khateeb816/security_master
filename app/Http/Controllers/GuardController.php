<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AssignCheckpoint;

class GuardController extends Controller
{
    public function index()
    {
        $guards = User::where('role', 'guard')->paginate(10);
        $clients = User::where('role', 'client')->get();
        return view('guards.index', compact('guards', 'clients'));
    }

    public function show($guardId)
    {
        $guard = User::where('role', 'guard')->findOrFail($guardId);
        $clients = User::where('role', 'client')->get();

        // Eager load checkpoint, branch, and client relationships
        $assignedCheckpoints = AssignCheckpoint::with(['checkpoint.branch', 'checkpoint.client'])
            ->where('guard_id', $guardId)
            ->get();

        return view('guards.show', compact('guard', 'clients', 'assignedCheckpoints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'status' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(12345678),
            'phone' => $request->phone,
            'status' => $request->status,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->postal_code,
            'country' => $request->country,
            'language' => $request->language,
            'cnic' => $request->cnic,
            'nfc_uid' => $request->nfc_uid,
            'notes' => $request->notes,
            'role' => 'guard'
        ]);

        return redirect()->back()->with('success', 'Guard Added Successfully');
    }

    public function update(Request $request, User $guard)
    {
        // Debug: Log the guard parameter
        Log::info('Update method called', [
            'guard_id' => $guard->id,
            'request_id' => $request->id,
            'request_all' => $request->all()
        ]);

        // Use route model binding - $guard is already the User model
        if (!$guard) {
            Log::error('Guard not found in update method', ['guard' => $guard]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guard not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Guard not found');
        }

        $guard->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->postal_code,
            'country' => $request->country,
            'language' => $request->language,
            'cnic' => $request->cnic,
            'nfc_uid' => $request->nfc_uid,
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Guard Updated Successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Guard Updated Successfully');
    }

    public function destroy(Request $request)
    {
        $guard = User::find($request->id);

        if (!$guard) {
            return redirect()->back()->with('error', 'Guard not found');
        }

        $guard->delete();

        return redirect()->back()->with('success', 'Guard deleted Successfully');
    }

    public function assignCheckpoint(Request $request)
    {
        $request->validate([
            'guard_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'checkpoint_id' => 'required|exists:checkpoints,id',
            'date' => 'required|date',
            'time' => 'required',
            'priority' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        // Check if this is an update (assignment_id is provided)
        if ($request->has('assignment_id') && $request->assignment_id) {
            // Update existing assignment
            $assignment = DB::table('assign_checkpoints')->find($request->assignment_id);

            if (!$assignment) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Assignment not found'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Assignment not found');
            }

            DB::table('assign_checkpoints')
                ->where('id', $request->assignment_id)
                ->update([
                    'checkpoint_id' => $request->checkpoint_id,
                    'date_to_check' => $request->date,
                    'time_to_check' => $request->time,
                    'priority' => (int) $request->priority,
                    'notes' => $request->notes,
                    'updated_at' => now(),
                ]);

            $message = 'Assignment updated successfully!';
        } else {
            // Create new assignment
            DB::table('assign_checkpoints')->insert([
                'guard_id' => $request->guard_id,
                'checkpoint_id' => $request->checkpoint_id,
                'date_to_check' => $request->date,
                'time_to_check' => $request->time,
                'priority' => (int) $request->priority,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $message = 'Checkpoint assigned to guard successfully!';
        }

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function removeAssignment($assignmentId)
    {
        $assignment = DB::table('assign_checkpoints')->find($assignmentId);

        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found');
        }

        DB::table('assign_checkpoints')->where('id', $assignmentId)->delete();

        return redirect()->back()->with('success', 'Assignment removed successfully');
    }
}
