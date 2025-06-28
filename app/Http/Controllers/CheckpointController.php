<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Checkpoint;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
            'client_id' => 'required|integer',
            'guard_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'date_to_check' => 'required|date',
            'time_to_check' => 'required',
            'description' => 'nullable|string',
            'nfc_tag' => [
                'nullable',
                'string',
                Rule::unique('checkpoints', 'nfc_tag')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })
            ],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Prepare checkpoint data
            $checkpointData = [
                'branch_id' => $validated['branch_id'],
                'user_id' => $validated['guard_id'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'date_to_check' => $validated['date_to_check'],
                'time_to_check' => $validated['time_to_check'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'radius' => $validated['radius'] ?? 50, // Default radius
                'status' => 'pending',
                'media' => json_encode([]), // Empty media array
                'priority' => $validated['priority'] ?? 0,
                'nfc_tag' => $validated['nfc_tag'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ];

            $checkpoint = Checkpoint::create($checkpointData);

            if (!$checkpoint) {
                DB::rollBack();
                Log::error('Checkpoint creation failed', ['data' => $checkpointData]);
                return back()->withInput()->with('error', 'Failed to add checkpoint. Please check your input and try again.');
            }
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checkpoint added successfully',
                    'data' => $checkpoint->load('branch')
                ]);
            }

            return redirect()
                ->route('clients.branches.checkpoints.index', [
                    'client' => $clientId,
                    'branch' => $branchId
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

            $clients = User::where('role', 'client')->orderBy('name')->get();
            $branches = Branch::where('user_id', $clientId)->orderBy('name')->get();
            $guards = User::where('role', 'guard')->orderBy('name')->get();

            return view('checkpoints.edit', [
                'checkpoint' => $checkpoint,
                'clients' => $clients,
                'branches' => $branches,
                'guards' => $guards,
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
            'description' => 'nullable|string',
            'date_to_check' => 'required|date',
            'time_to_check' => 'required',
            'nfc_tag' => [
                'nullable',
                'string',
                Rule::unique('checkpoints', 'nfc_tag')
                    ->ignore($checkpointId)
                    ->where('branch_id', $branchId)
            ],
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:0',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'client_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'guard_id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $checkpoint = Checkpoint::where('branch_id', $branchId)
                ->findOrFail($checkpointId);

            $checkpoint->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'date_to_check' => $validated['date_to_check'],
                'time_to_check' => $validated['time_to_check'],
                'nfc_tag' => $validated['nfc_tag'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'radius' => $validated['radius'] ?? 50,
                'priority' => $validated['priority'] ?? 0,
                'is_active' => $request->has('is_active') ? $validated['is_active'] : false,
                'client_id' => $validated['client_id'],
                'branch_id' => $validated['branch_id'],
                'user_id' => $validated['guard_id'],
            ]);

            DB::commit();

            return redirect()
                ->route('clients.branches.checkpoints.index', [
                    'client' => $validated['client_id'],
                    'branch' => $validated['branch_id'],
                    'client_id' => $validated['client_id'],
                    'branch_id' => $validated['branch_id']
                ])
                ->with('success', 'Checkpoint updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

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
        $checkpoint = Checkpoint::where('branch_id', $branchId)
            ->findOrFail($checkpointId);

        $qrData = $checkpoint->qr_code ?? $checkpoint->id;

        return view('checkpoints.qrcode', [
            'checkpoint' => $checkpoint,
            'qr' => QrCode::size(300)->generate($qrData)
        ]);
    }
}
