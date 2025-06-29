<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Please login to access this page.',
            ]);
        }

        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Access denied. Only administrators can access the dashboard.',
            ]);
        }

        return $next($request);
    }
}
