<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect based on role
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('login');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to find user by email or username
        $user = User::where('email', $request->email)
                   ->orWhere('name', $request->email)
                   ->first();

        if (!$user) {


            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->withInput($request->only('email'));
        }

        // Check if user is active
        if ($user->status !== 'active') {


            return back()->withErrors([
                'email' => 'Your account is not active. Please contact administrator.',
            ])->withInput($request->only('email'));
        }

        // Attempt to authenticate
        if (Auth::attempt([
            'id' => $user->id,
            'password' => $request->password
        ], $request->boolean('remember'))) {



            // Redirect based on role
            return $this->redirectBasedOnRole();
        }


        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
    
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard');

            case 'client':
                return redirect()->route('login')->withErrors([
                    'email' => 'Clients cannot access the dashboard. Please use the mobile app.',
                ]);

            case 'guard':
                return redirect()->route('login')->withErrors([
                    'email' => 'Guards cannot access the dashboard. Please use the mobile app.',
                ]);

            default:
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Invalid user role.',
                ]);
        }
    }

    /**
     * Show access denied page
     */
    public function accessDenied()
    {
        return view('auth.access-denied');
    }
}