<?php

namespace App\Http\Controllers;

use App\Mail\RegisterOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        // Clean phone before validation:
        // 0946-979-4208 becomes 09469794208
        $request->merge([
            'phone' => $request->phone
                ? preg_replace('/\D/', '', $request->phone)
                : null,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'regex:/^09\d{9}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'The phone number must be 11 digits and start with 09.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);

        $otp = (string) random_int(100000, 999999);

        /*
         * Store registration data in session first.
         * User account is NOT created yet.
         * Account will only be created after correct OTP.
         */
        session([
            'register_otp_hash' => Hash::make($otp),
            'register_otp_expires_at' => now()->addMinutes(10)->toDateTimeString(),
            'register_data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role_id' => 2, // Customer
            ],
        ]);

        Mail::to($validated['email'])->send(new RegisterOtpMail($otp));

        return redirect()
            ->route('register.otp.form')
            ->with('success', 'We sent a 6-digit OTP to your email. Please verify it to create your account.');
    }

    public function showRegisterOtpForm()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }

        if (! session()->has('register_data')) {
            return redirect()
                ->route('register')
                ->with('error', 'Please register first.');
        }

        return view('auth.verify-register-otp');
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Please enter the OTP code.',
            'otp.digits' => 'The OTP must be 6 digits.',
        ]);

        if (! session()->has('register_data')) {
            return redirect()
                ->route('register')
                ->with('error', 'Registration session expired. Please register again.');
        }

        $expiresAt = Carbon::parse(session('register_otp_expires_at'));

        if (now()->greaterThan($expiresAt)) {
            session()->forget([
                'register_otp_hash',
                'register_otp_expires_at',
                'register_data',
            ]);

            return redirect()
                ->route('register')
                ->with('error', 'OTP expired. Please register again.');
        }

        if (! Hash::check($request->otp, session('register_otp_hash'))) {
            return back()
                ->withInput()
                ->withErrors([
                    'otp' => 'Invalid OTP. Please try again.',
                ]);
        }

        $data = session('register_data');

        if (User::where('email', $data['email'])->exists()) {
            session()->forget([
                'register_otp_hash',
                'register_otp_expires_at',
                'register_data',
            ]);

            return redirect()
                ->route('register')
                ->with('error', 'This email is already registered. Please login instead.');
        }

        /*
         * Account is created here only after correct OTP.
         */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'role_id' => 2, // Customer
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        session()->forget([
            'register_otp_hash',
            'register_otp_expires_at',
            'register_data',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user)
            ->with('success', 'Account created successfully.');
    }

    public function resendRegisterOtp()
    {
        if (! session()->has('register_data')) {
            return redirect()
                ->route('register')
                ->with('error', 'Please register first.');
        }

        $data = session('register_data');
        $otp = (string) random_int(100000, 999999);

        session([
            'register_otp_hash' => Hash::make($otp),
            'register_otp_expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ]);

        Mail::to($data['email'])->send(new RegisterOtpMail($otp));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

   
}