<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please log in first.');
        }

        
        if ((int) auth()->user()->role_id !== 4) {
            abort(403, 'Unauthorized. Manager access only.');
        }

        return $next($request);
    }
}