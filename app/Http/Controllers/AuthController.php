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

            // If guest already selected a car before login, continue booking after auth
            if (session()->has('guest_booking_payload') && (int) $user->role_id === 2) {
                return redirect()->route('user.booking.continue');
            }

            if ((int) $user->role_id === 1) {
                return redirect()->route('admin.dashboard');
            } elseif ((int) $user->role_id === 2) {
                return redirect()->route('user.browse');
            } elseif ((int) $user->role_id === 3) {
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

            // If guest already selected a car before register, continue booking after auth
            if (session()->has('guest_booking_payload') && (int) $user->role_id === 2) {
                return redirect()->route('user.booking.continue');
            }

            if ((int) $user->role_id === 1) {
                return redirect()->route('admin.dashboard');
            } elseif ((int) $user->role_id === 2) {
                return redirect()->route('user.browse');
            } elseif ((int) $user->role_id === 3) {
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // If guest booking exists, continue booking then go to payment
            if (session()->has('guest_booking_payload') && (int) $user->role_id === 2) {
                return redirect()->route('user.booking.continue');
            }

            if ((int) $user->role_id === 1) {
                return redirect()->route('admin.dashboard');
            } elseif ((int) $user->role_id === 2) {
                return redirect()->route('user.browse');
            } elseif ((int) $user->role_id === 3) {
                return redirect()->route('staff.dashboard');
            }

            return redirect('/');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Invalid email or password. Please try again.'
            ]);
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

        // If guest booking exists, continue booking then go to payment
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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password',
        ], 401);
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