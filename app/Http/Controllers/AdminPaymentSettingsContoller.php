<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminPaymentSettingsContoller extends Controller
{
    
  public function edit()
    {
        $setting = PaymentSetting::where('provider', 'paypal')->first();
        
        // If no setting exists, create one
        if (!$setting) {
            $setting = PaymentSetting::create([
                'provider' => 'paypal',
                'environment' => 'sandbox',
                'client_id' => '',
                'client_secret' => '',
                'business_email' => '',
                'active' => false,
            ]);
        }

        return view('admin.adminsettings', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'environment'      => 'required|in:sandbox,live',
            'client_id'        => 'required|string',
            'client_secret'    => 'required|string',
            'business_email'   => 'required|email',
            'active'           => 'nullable|boolean',
        ]);

        $setting = PaymentSetting::where('provider', 'paypal')->first();
        
        if (!$setting) {
            $setting = PaymentSetting::create([
                'provider' => 'paypal',
                'environment' => 'sandbox',
                'client_id' => '',
                'client_secret' => '',
                'business_email' => '',
                'active' => false,
            ]);
        }

        $setting->update([
            'environment'     => $validated['environment'],
            'client_id'       => $validated['client_id'],
            'client_secret'   => $validated['client_secret'],
            'business_email'  => $validated['business_email'],
            'active'          => $request->boolean('active'),
        ]);

        return back()->with('success', 'PayPal settings updated successfully.');
    }
    }

