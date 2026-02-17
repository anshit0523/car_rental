<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;

class PaymentSetting extends Model
{
   protected $table = 'payment_settings';

    protected $fillable = [
        'provider',
        'environment',
        'client_id',
        'client_secret',
        'business_email',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $hidden = [
        'client_secret',
    ];

    // Override getAttribute to handle decryption errors gracefully
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        // If it's an encrypted field and decryption fails, return empty string
        if (in_array($key, ['client_id', 'client_secret'])) {
            try {
                return decrypt($value);
            } catch (DecryptException $e) {
                return '';
            }
        }

        return $value;
    }

    // Override setAttribute to handle encryption
    public function setAttribute($key, $value)
    {
        if (in_array($key, ['client_id', 'client_secret']) && !empty($value)) {
            $value = encrypt($value);
        }

        parent::setAttribute($key, $value);
    }
}
