<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $redirectTo = '/dashboard';

   

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
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
        
        // Use role constants for maintainability
        return match ($user->role_id) {
            1 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('dashboard'),
            default => redirect()->intended($this->redirectTo),
        };
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
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect($this->redirectTo);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
