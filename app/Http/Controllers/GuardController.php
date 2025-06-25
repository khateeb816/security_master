<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuardController extends Controller
{
    public function index()
    {
        $guards = User::where('role', 'guard')->paginate(10);
        return view('guards.index', compact('guards'));
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

    public function update(Request $request)
    {
        $guard = User::find($request->id);
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
}
