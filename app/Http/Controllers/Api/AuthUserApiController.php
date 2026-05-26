<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegisterOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthUserApiController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        if ((int) $user->role_id !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'This app login is for customers only.',
            ], 403);
        }

        $token = $user->createToken('customer-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $request->merge([
            'phone' => $request->phone ? preg_replace('/\D/', '', $request->phone) : null,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'regex:/^09\d{9}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $otp = (string) random_int(100000, 999999);
        $cacheKey = 'api_register_' . strtolower($validated['email']);

        Cache::put($cacheKey, [
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
            'data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role_id' => 2,
            ],
        ], now()->addMinutes(10));

        Mail::to($validated['email'])->send(new RegisterOtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to email. Please verify to create account.',
        ]);
    }

    public function verifyRegisterOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $cacheKey = 'api_register_' . strtolower($validated['email']);
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            return response()->json([
                'success' => false,
                'message' => 'Registration session expired. Please register again.',
            ], 422);
        }

        if (now()->greaterThan(Carbon::parse($cached['expires_at']))) {
            Cache::forget($cacheKey);

            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please register again.',
            ], 422);
        }

        if (!Hash::check($validated['otp'], $cached['otp_hash'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        if (User::where('email', $cached['data']['email'])->exists()) {
            Cache::forget($cacheKey);

            return response()->json([
                'success' => false,
                'message' => 'This email is already registered.',
            ], 422);
        }

        $user = User::create($cached['data']);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Cache::forget($cacheKey);

        $token = $user->createToken('customer-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }


    public function sendForgotPasswordOtp(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $otp = rand(100000, 999999);

    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $validated['email']],
        [
            'token' => $otp,
            'created_at' => now(),
        ]
    );

    Mail::raw("Your Dumaguete EZE password reset OTP is: {$otp}", function ($message) use ($validated) {
        $message->to($validated['email'])
            ->subject('Password Reset OTP');
    });

    return response()->json([
        'success' => true,
        'message' => 'OTP sent to your email.',
    ]);
}

public function resetPasswordWithOtp(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|string|size:6',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $reset = DB::table('password_reset_tokens')
        ->where('email', $validated['email'])
        ->where('token', $validated['otp'])
        ->first();

    if (!$reset) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP.',
        ], 422);
    }

    if (Carbon::parse($reset->created_at)->addMinutes(10)->isPast()) {
        return response()->json([
            'success' => false,
            'message' => 'OTP has expired.',
        ], 422);
    }

    $user = User::where('email', $validated['email'])->firstOrFail();

    $user->update([
        'password' => Hash::make($validated['password']),
    ]);

    DB::table('password_reset_tokens')
        ->where('email', $validated['email'])
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Password reset successfully.',
    ]);
}

}