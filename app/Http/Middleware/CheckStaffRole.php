<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login first.');
        }

        // Allow only staff
        if ((int) Auth::user()->role_id !== 3) {
            if ((int) Auth::user()->role_id === 1) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Access denied. Staff only.');
            }

            return redirect()->route('user.browse')
                ->with('error', 'Access denied. Staff only.');
        }

        return $next($request);
    }
}