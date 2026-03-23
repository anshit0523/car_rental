<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPaymentSettingController extends Controller
{
    public function edit()
    {
        $setting = PaymentSetting::first();

        if (!$setting) {
            $setting = PaymentSetting::create([
                'gcash_account_name' => 'Car Rental PH',
                'gcash_number' => '0912-345-6789',
                'bank_name' => 'BDO',
                'bank_account_name' => 'Car Rental PH',
                'bank_account_number' => '1234 5678 9012',
            ]);
        }

        return view('admin.adminsettings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gcash_account_name' => 'nullable|string|max:255',
            'gcash_number' => 'nullable|string|max:255',
            'gcash_qr_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
        ]);

        $setting = PaymentSetting::first() ?? new PaymentSetting();

        $setting->gcash_account_name = $request->gcash_account_name;
        $setting->gcash_number = $request->gcash_number;
        $setting->bank_name = $request->bank_name;
        $setting->bank_account_name = $request->bank_account_name;
        $setting->bank_account_number = $request->bank_account_number;

        if ($request->hasFile('gcash_qr_image')) {
            if ($setting->gcash_qr_image && Storage::disk('public')->exists($setting->gcash_qr_image)) {
                Storage::disk('public')->delete($setting->gcash_qr_image);
            }

            $setting->gcash_qr_image = $request->file('gcash_qr_image')->store('payment-settings', 'public');
        }

        $setting->save();

        return back()->with('success', 'Payment settings updated successfully.');
    }
}