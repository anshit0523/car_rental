<?php

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'),
    
    'sandbox' => [
        'username'      => env('PAYPAL_SANDBOX_API_USERNAME', ''),
        'password'      => env('PAYPAL_SANDBOX_API_PASSWORD', ''),
        'signature'     => env('PAYPAL_SANDBOX_API_SIGNATURE', ''),
        'certificate'   => env('PAYPAL_SANDBOX_API_CERTIFICATE', ''),
        'client_id'     => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    ],
    
    'live' => [
        'username'      => env('PAYPAL_LIVE_API_USERNAME', ''),
        'password'      => env('PAYPAL_LIVE_API_PASSWORD', ''),
        'signature'     => env('PAYPAL_LIVE_API_SIGNATURE', ''),
        'certificate'   => env('PAYPAL_LIVE_API_CERTIFICATE', ''),
        'client_id'     => env('PAYPAL_LIVE_CLIENT_ID'),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET'),
    ],

    'payment_action' => 'Sale',
    'currency'       => env('PAYPAL_CURRENCY', 'PHP'),
    'billing_type'   => 'MerchantInitiatedBilling',
    'notify_url'     => env('PAYPAL_NOTIFY_URL', ''),
    'locale'         => 'en_US',
    'validate_ssl'   => true,
];