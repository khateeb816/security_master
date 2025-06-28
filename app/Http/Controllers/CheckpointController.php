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
        // Debug logging
        Log::info('CheckpointController@index called', [
            'clientId' => $clientId,
            'branchId' => $branchId,
            'wantsJson' => request()->wantsJson(),
            'isAjax' => request()->ajax(),
            'url' => request()->url()
        ]);

        $clients = User::where('role', 'client')->orderBy('name')->get();

        // If no clients exist, redirect to clients page
        if ($clients->isEmpty()) {
            return redirect()
                ->route('clients.index')
                ->with('warning', 'Please add a client and branch first before managing checkpoints.');
        }

        // Initialize variables
        $branches = collect();
        $checkpoints = collect();

        // If client is selected, get branches for that client
        if ($clientId) {
            $branches = Branch::select(['id', 'name'])
                ->where('user_id', $clientId)
                ->orderBy('name')
                ->get();

            // If branch is selected, get checkpoints for that branch
            if ($branchId) {
                $checkpoints = Checkpoint::where('branch_id', $branchId)
                    ->with('branch')
                    ->orderBy('name')
                    ->get();
            }
        } else {
            // If no client is selected, show all checkpoints
            $checkpoints = Checkpoint::with('branch.client')
                ->orderBy('name')
                ->get();
        }

        // Debug: Log the checkpoint count
        Log::info('Checkpoints found: ' . $checkpoints->count());
        Log::info('Client ID: ' . $clientId);
        Log::info('Branch ID: ' . $branchId);

        $guards = User::where('role', 'guard')
            ->orderBy('name')
            ->get();

        // Return JSON response for AJAX requests
        if (request()->wantsJson()) {
            Log::info('Returning JSON response with ' . $checkpoints->count() . ' checkpoints');
            return response()->json($checkpoints);
        }

        // Return view for regular requests
        return view('checkpoints.index', [
            'clients' => $clients,
            'branches' => $branches,
            'checkpoints' => $checkpoints,
            'selectedClient' => $clientId ? (int) $clientId : null,
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();
            $checkpoint = Checkpoint::create([
                'branch_id' => $validated['branch_id'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'radius' => $validated['radius'],
                'is_active' => $request->has('is_active') ? $validated['is_active'] : false,
            ]);
            DB::commit();
            return redirect()->route('clients.branches.checkpoints.index', [
                'client' => $validated['client_id'],
                'branch' => $validated['branch_id'],
                'client_id' => $validated['client_id'],
                'branch_id' => $validated['branch_id']
            ])->with('success', 'Checkpoint added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save checkpoint: ' . $e->getMessage());
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
            'branch_id' => 'required|exists:branches,id',
            'client_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();
            $checkpoint = Checkpoint::where('branch_id', $branchId)->findOrFail($checkpointId);
            $checkpoint->update([
                'branch_id' => $validated['branch_id'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'radius' => $validated['radius'],
                'is_active' => $request->has('is_active') ? $validated['is_active'] : false,
            ]);
            DB::commit();
            return redirect()->route('clients.branches.checkpoints.index', [
                'client' => $validated['client_id'],
                'branch' => $validated['branch_id'],
                'client_id' => $validated['client_id'],
                'branch_id' => $validated['branch_id']
            ])->with('success', 'Checkpoint updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update checkpoint: ' . $e->getMessage());
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
