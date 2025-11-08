<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // If already logged in, redirect to appropriate dashboard
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->role_id == 1) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role_id == 2) {
                return redirect()->route('user.dashboard');
            }
            
            return redirect('/');
        }
        
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        // If already logged in, redirect to dashboard
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->role_id == 1) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role_id == 2) {
                return redirect()->route('user.dashboard');
            }
            
            return redirect('/');
        }
        
        return view('auth.register');
    }

    public function login(Request $request) 
    { 
        $credentials = $request->validate([ 
            'email' => 'required|email', 
            'password' => 'required', 
        ]); 

        if (Auth::attempt($credentials)) { 
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on role_id
            // CRITICAL: Use correct route names!
            if ($user->role_id == 1) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role_id == 2) {
                return redirect()->route('user.dashboard');
            }
            
            return redirect('/');
        } 

        return back()
            ->withInput($request->only('email'))
            ->with('loginError', 'Invalid email or password. Please try again.'); 
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Register as regular user
        ]);

        Auth::login($user);
        return redirect()->route('user.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}