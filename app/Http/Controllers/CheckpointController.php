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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, $clientId = null, $branchId = null)
    {
        // If this is an AJAX/JSON request for checkpoints of a branch
        if ($request->wantsJson() || $request->ajax()) {
            if ($branchId) {
                $checkpoints = \App\Models\Checkpoint::where('branch_id', $branchId)->get();
                return response()->json(['checkpoints' => $checkpoints]);
            } else {
                return response()->json(['checkpoints' => []]);
            }
        }

        $clients = User::where('role', 'client')->orderBy('name')->get();
        $branches = collect();
        $checkpointsQuery = Checkpoint::with(['branch.client']);

        $selectedClient = $request->input('client_id');
        $selectedBranch = $request->input('branch_id');
        $search = $request->input('search');
        $date = $request->input('date');

        if ($selectedClient) {
            $checkpointsQuery->where('client_id', $selectedClient);
            $branches = Branch::where('user_id', $selectedClient)->orderBy('name')->get();
        }
        if ($selectedBranch) {
            $checkpointsQuery->where('branch_id', $selectedBranch);
        }
        if ($search) {
            $checkpointsQuery->where('name', 'like', "%$search%");
        }
        if ($date) {
            $checkpointsQuery->whereDate('created_at', $date);
        }

        $checkpoints = $checkpointsQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $guards = User::where('role', 'guard')->orderBy('name')->get();

        return view('checkpoints.index', [
            'clients' => $clients,
            'branches' => $branches,
            'checkpoints' => $checkpoints,
            'selectedClient' => $selectedClient,
            'selectedBranch' => $selectedBranch,
            'search' => $search,
            'date' => $date,
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


            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checkpoint added successfully.',
                    'data' => $checkpoint
                ]);
            }

            return redirect()->route('clients.branches.checkpoints.index', [
                'client' => $validated['client_id'],
                'branch' => $validated['branch_id'],
                'client_id' => $validated['client_id'],
                'branch_id' => $validated['branch_id']
            ])->with('success', 'Checkpoint added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save checkpoint: ' . $e->getMessage()
                ], 500);
            }

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
        $checkpoint = Checkpoint::where('branch_id', $branchId)->findOrFail($checkpointId);
        $checkpoint->delete();
        return redirect()->route('checkpoints.all')->with('success', 'Checkpoint deleted successfully.');
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
