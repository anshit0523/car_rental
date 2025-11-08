<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        // Allow only admins (role_id = 1)
        if (Auth::user()->role_id != 1) {
            return redirect('/dashboard')->with('error', 'Access denied. Admins only.');
        }

        return $next($request);
    }
}
