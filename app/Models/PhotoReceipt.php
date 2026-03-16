<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoReceipt extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_id',
        'user_id',
        'image_path',
        'payment_method',
        'status',
        'admin_note',
        'verified_by',
        'verified_at'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
