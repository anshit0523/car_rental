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
        if (auth()->check()) {
            $user = auth()->user();

            if (session()->has('guest_booking_payload') && (int) $user->role_id === 2) {
                return redirect()->route('user.booking.continue');
            }

            if ((int) $user->role_id === 2) {
                return redirect()->route('user.browse');
            }

            if ((int) $user->role_id === 1) {
                return redirect()->route('admin.dashboard');
            }

            if ((int) $user->role_id === 3) {
                return redirect()->route('staff.dashboard');
            }

            return redirect('/');
        }

        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (session()->has('guest_booking_payload') && (int) $user->role_id === 2) {
                return redirect()->route('user.booking.continue');
            }

            if ((int) $user->role_id === 2) {
                return redirect()->route('user.browse');
            }

            if ((int) $user->role_id === 1) {
                return redirect()->route('admin.dashboard');
            }

            if ((int) $user->role_id === 3) {
                return redirect()->route('staff.dashboard');
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

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Invalid email or password. Please try again.'
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Only customers can use /login
        if ((int) $user->role_id !== 2) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'This login is for customers only.'
                ]);
        }

        if (session()->has('guest_booking_payload')) {
            return redirect()->route('user.booking.continue');
        }

        return redirect()->route('user.browse');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role_id' => 2,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if (session()->has('guest_booking_payload')) {
            return redirect()->route('user.booking.continue');
        }

        return redirect()->route('user.browse');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // controller for api ---------------------------------------

    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        $user = Auth::user();

        if ((int) $user->role_id !== 2) {
            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'This login is for customers only.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
        ]);
    }

    public function apiRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role_id' => 2,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => $user,
        ], 201);
    }
}