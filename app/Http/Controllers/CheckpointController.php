<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Checkpoint;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckpointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int|null  $clientId
     * @param  int|null  $branchId
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index($clientId = null, $branchId = null)
    {
        $clients = User::where('role', 'client')->orderBy('name')->get();

        // If no clients exist, redirect to clients page
        if ($clients->isEmpty()) {
            return redirect()
                ->route('clients.index')
                ->with('warning', 'Please add a client and branch first before managing checkpoints.');
        }

        // If no client is selected, use the first one
        if (!$clientId) {
            $firstClient = $clients->first();
            $firstBranch = $firstClient->branches()->first();

            if ($firstBranch) {
                return redirect()->route('clients.branches.checkpoints.index', [
                    'client' => $firstClient->id,
                    'branch' => $firstBranch->id,
                    'client_id' => $firstClient->id,
                    'branch_id' => $firstBranch->id
                ]);
            } else {
                return redirect()
                    ->route('clients.index')
                    ->with('warning', 'Please add a branch to the client before managing checkpoints.');
            }
        }

        // Get branches for the selected client
        $branches = Branch::select(['id', 'name'])
            ->where('user_id', $clientId)
            ->orderBy('name')
            ->get();

        // If no branch is selected, use the first one
        if (!$branchId && $branches->isNotEmpty()) {
            return redirect()->route('clients.branches.checkpoints.index', [
                'client' => $clientId,
                'branch' => $branches->first()->id,
                'client_id' => $clientId,
                'branch_id' => $branches->first()->id
            ]);
        }

        // Get checkpoints for the selected branch
        $checkpoints = $branchId
            ? Checkpoint::where('branch_id', $branchId)
                ->with('branch')
                ->orderBy('name')
                ->get()
            : collect();

        $guards = User::where('role', 'guard')
            ->orderBy('name')
            ->get();

        // Return JSON response for AJAX requests
        if (request()->wantsJson()) {
            return response()->json($checkpoints);
        }

        // Return view for regular requests
        return view('checkpoints.index', [
            'clients' => $clients,
            'branches' => $branches,
            'checkpoints' => $checkpoints,
            'selectedClient' => (int) $clientId,
            'selectedBranch' => $branchId ? (int) $branchId : null,
            'guards' => $guards,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $clientId
     * @param  int  $branchId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $clientId, $branchId)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'client_id' => 'required|exists:users,id',
            'guard_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'date_to_check' => 'required',
            'time_to_check' => 'required',
            'description' => 'nullable|string',
            'nfc_tag' => [
                'nullable',
                'string',
                Rule::unique('checkpoints', 'nfc_tag')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })->ignore($request->input('checkpointId'))
            ],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();
            // Use only $validated data, and set status here
            $checkpointData = array_merge($validated, [
                'user_id' => $validated['guard_id'],
                'status' => 'pending',
            ]);
            unset($checkpointData['guard_id']); // Not a DB column
            $checkpoint = Checkpoint::create($checkpointData);

            if (!$checkpoint) {
                DB::rollBack();
                \Log::error('Checkpoint creation failed', ['data' => $checkpointData]);
                return back()->withInput()->with('error', 'Failed to add checkpoint. Please check your input and try again.');
            }
            DB::commit();

            return redirect()
                ->route('clients.branches.checkpoints.index', [
                    'client' => $checkpoint->client_id,
                    'branch' => $checkpoint->branch_id
                ])
                ->with('success', 'Checkpoint added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save checkpoint',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to save checkpoint: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $clientId
     * @param  int  $branchId
     * @param  int  $checkpointId
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show($clientId, $branchId, $checkpointId)
    {
        try {
            $checkpoint = Checkpoint::with('branch.client')
                ->where('branch_id', $branchId)
                ->findOrFail($checkpointId);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $checkpoint
                ]);
            }

            return view('checkpoints.show', [
                'checkpoint' => $checkpoint,
                'client' => $checkpoint->branch->client,
                'branch' => $checkpoint->branch
            ]);
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch checkpoint',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to fetch checkpoint: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $clientId
     * @param  int  $branchId
     * @param  int  $checkpointId
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function edit($clientId, $branchId, $checkpointId)
    {
        try {
            $checkpoint = Checkpoint::with('branch.client')
                ->where('branch_id', $branchId)
                ->findOrFail($checkpointId);

            $clients = User::where('role', 'client')->orderBy('company_name')->get();
            $branches = Branch::where('client_id', $clientId)->orderBy('branch_name')->get();

            return view('checkpoints.edit', [
                'checkpoint' => $checkpoint,
                'clients' => $clients,
                'branches' => $branches,
                'client' => $checkpoint->branch->client
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $clientId
     * @param  int  $branchId
     * @param  int  $checkpointId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $clientId, $branchId, $checkpointId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'point_code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'nfc_tag' => [
                'nullable',
                'string',
                Rule::unique('checkpoints', 'nfc_tag')
                    ->ignore($checkpointId)
                    ->where('branch_id', $branchId)
            ],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'geofence_radius' => 'nullable|integer|min:0',
            'geofence_enabled' => 'boolean',
            'site' => 'nullable|string|max:255',
            'client_site_code' => 'nullable|string|max:100',
            'checkpoint_code' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $checkpoint = Checkpoint::where('branch_id', $branchId)
                ->findOrFail($checkpointId);

            $checkpoint->update($validated);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checkpoint updated successfully',
                    'data' => $checkpoint->load('branch')
                ]);
            }

            return redirect()
                ->route('clients.branches.checkpoints.index', [
                    'client' => $clientId,
                    'branch' => $branchId,
                    'client_id' => $clientId,
                    'branch_id' => $branchId
                ])
                ->with('success', 'Checkpoint updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update checkpoint',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update checkpoint: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $clientId
     * @param  int  $branchId
     * @param  int  $checkpointId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($clientId, $branchId, $checkpointId)
    {
        try {
            DB::beginTransaction();

            $checkpoint = Checkpoint::where('branch_id', $branchId)
                ->findOrFail($checkpointId);

            $checkpoint->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checkpoint deleted successfully'
                ]);
            }

            return redirect()
                ->route('clients.branches.checkpoints.index', [
                    'client' => $clientId,
                    'branch' => $branchId,
                    'client_id' => $clientId,
                    'branch_id' => $branchId
                ])
                ->with('success', 'Checkpoint deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete checkpoint',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete checkpoint: ' . $e->getMessage());
        }
    }

    /**
     * Get the QR code for the specified checkpoint.
     *
     * @param  int  $clientId
     * @param  int  $branchId
     * @param  int  $checkpointId
     * @return \Illuminate\Http\Response
     */
    public function getQrCode($clientId, $branchId, $checkpointId)
    {
        try {
            $checkpoint = Checkpoint::where('branch_id', $branchId)
                ->findOrFail($checkpointId);

            // In a real app, you would generate a QR code here
            // For now, we'll just return the QR code data
            return response()->json([
                'success' => true,
                'data' => [
                    'qr_code' => $checkpoint->qr_code,
                    'name' => $checkpoint->name,
                    'checkpoint_id' => $checkpoint->id,
                    'branch_id' => $branchId,
                    'client_id' => $clientId
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch QR code',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
