<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function redirectByRole($user)
    {
        if (! $user) {
            return redirect()->route('login');
        }

        $roleId = (int) $user->role_id;

        // Customer
        if ($roleId === 2) {
            if (session()->has('guest_booking_payload')) {
                return redirect()->route('user.booking.continue');
            }

            return redirect()->route('user.browse');
        }

        // Admin
        if ($roleId === 1) {
            return redirect()->route('admin.dashboard');
        }

        // Staff
        if ($roleId === 3) {
            return redirect()->route('staff.dashboard');
        }

        // Manager
        if ($roleId === 4) {
            return redirect()->route('manager.dashboard');
        }

        return redirect('/');
    }

    public function showLoginForm()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }

        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }

        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Invalid email or password. Please try again.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        /*
         * This /login page is for customers only.
         * Admin, Staff, and Manager should login at /admin/login.
         */
        if ((int) $user->role_id !== 2) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'This login is for customers only. Please use the admin/staff/manager login page.',
                ]);
        }

        return $this->redirectByRole($user);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Customer
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // API controller methods ---------------------------------------

    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Customer
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => $user,
        ], 201);
    }
}