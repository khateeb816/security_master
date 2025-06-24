<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        \Log::info('Starting client creation', ['data' => $request->all()]);
        
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            \Log::info('Validated data:', $validated);
            
            $client = Client::create($validated);
            \Log::info('Client created successfully', ['client_id' => $client->id]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client created successfully',
                    'redirect' => route('clients.index')
                ]);
            }
            
            return redirect()->route('clients.index')
                ->with('success', 'Client created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating client: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating client: ' . $e->getMessage(),
                    'errors' => $e instanceof \Illuminate\Validation\ValidationException 
                        ? $e->errors() 
                        : []
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Error creating client: ' . $e->getMessage());
        }
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            \Log::info('Updating client with data:', $validated);
            
            $client->update($validated);
            
            // Log the updated client data
            \Log::info('Client updated successfully', [
                'client_id' => $client->id,
                'arc_id' => $client->arc_id,
                'all_attributes' => $client->toArray()
            ]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client updated successfully',
                    'redirect' => route('clients.index')
                ]);
            }
            
            return redirect()->route('clients.index')
                ->with('success', 'Client updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating client: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Error updating client: ' . $e->getMessage());
        }
    }

    public function destroy(Client $client)
    {
        try {
            DB::beginTransaction();
            
            $client->delete();
            
            DB::commit();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client deleted successfully'
                ]);
            }
            
            return redirect()->route('clients.index')
                ->with('success', 'Client deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting client: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->with('error', 'Error deleting client: ' . $e->getMessage());
        }
    }
}
