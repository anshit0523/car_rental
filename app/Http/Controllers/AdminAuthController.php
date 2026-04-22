<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ((int) $user->role_id === 1) {
                return redirect()->route('admin.dashboard');
            }

            if ((int) $user->role_id === 4) {
                return redirect()->route('manager.dashboard');
            }

            if ((int) $user->role_id === 3) {
                return redirect()->route('staff.dashboard');
            }

            if ((int) $user->role_id === 2) {
                return redirect()->route('user.browse');
            }

            return redirect('/');
        }

        return view('admin.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Invalid email or password. Please try again.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (!in_array((int) $user->role_id, [1, 3, 4], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'This login is for admin, manager, and staff only.',
                ]);
        }

        if ((int) $user->role_id === 1) {
            return redirect()->route('admin.dashboard');
        }

        if ((int) $user->role_id === 4) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('staff.dashboard');
    }
}