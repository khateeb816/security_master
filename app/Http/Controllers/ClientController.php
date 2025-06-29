<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::where("role", "client")->paginate(10);
        return view('clients.index', compact('clients'));
    }

   public function store(StoreClientRequest $request)
{
    // Create the user
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
        'role' => 'client'
    ]);

    return redirect()->back()->with('success', 'Client Added Successfully');
}

    public function update(UpdateClientRequest $request)
    {
        $client = User::find($request->id);
        $client->update([
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
        return redirect()->back()->with("success", "Client Updated");
    }

    public function destroy(Request $request)
    {
        $client = User::find($request->id);

        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }

        $client->delete();

        return redirect()->back()->with('success', 'Client deleted');
    }
}
