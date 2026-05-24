<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangeOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserControlleApi extends Controller
{
   public function sendChangePasswordOtp(Request $request)
{
    $validated = $request->validate([
        'current_password' => 'required|string',
    ]);

    $user = $request->user();

    if (!$user || !$user->email) {
        return response()->json([
            'success' => false,
            'message' => 'User email not found.',
        ], 422);
    }

    if (!Hash::check($validated['current_password'], $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Current password is incorrect.',
        ], 422);
    }

    $otp = (string) random_int(100000, 999999);

    DB::table('password_change_otps')
        ->where('user_id', $user->id)
        ->whereNull('used_at')
        ->update([
            'used_at' => now(),
            'updated_at' => now(),
        ]);

    DB::table('password_change_otps')->insert([
        'user_id' => $user->id,
        'email' => $user->email,
        'otp' => $otp,
        'expires_at' => now()->addMinutes(5),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Mail::to($user->email)->send(new PasswordChangeOtpMail($otp, $user));

    return response()->json([
        'success' => true,
        'message' => 'OTP has been sent to your email.',
    ]);
}


    public function changePasswordWithOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|string|size:6',
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!$user || !$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'User email not found.',
            ], 422);
        }

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $otpRecord = DB::table('password_change_otps')
            ->where('user_id', $user->id)
            ->where('email', $user->email)
            ->where('otp', $validated['otp'])
            ->whereNull('used_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        DB::transaction(function () use ($user, $validated, $otpRecord) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($validated['password']),
                    'updated_at' => now(),
                ]);

            DB::table('password_change_otps')
                ->where('id', $otpRecord->id)
                ->update([
                    'used_at' => now(),
                    'updated_at' => now(),
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    public function updateProfile(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => ['nullable', 'regex:/^09\d{9}$/'],
        'address' => 'nullable|string|max:255',
    ], [
        'phone.regex' => 'Phone number must be 11 digits and start with 09.',
    ]);

    $user->update([
        'name' => $validated['name'],
        'phone' => $validated['phone'] ?? null,
        'address' => $validated['address'] ?? null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'user' => $user->fresh(),
    ]);
}

}