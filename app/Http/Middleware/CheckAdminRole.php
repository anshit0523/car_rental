<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login first.');
        }

        // Allow only admins
        if ((int) Auth::user()->role_id !== 1) {
            if ((int) Auth::user()->role_id === 3) {
                return redirect()->route('staff.dashboard')
                    ->with('error', 'Access denied. Admins only.');
            }

            return redirect()->route('user.browse')
                ->with('error', 'Access denied. Admins only.');
        }

        return $next($request);
    }
}