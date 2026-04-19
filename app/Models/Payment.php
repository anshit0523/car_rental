<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   protected $fillable = [
    'booking_id',
    'payment_method_id',
    'payment_status_id',
    'payment_date',
    'amount',
    'transaction_id',
    'notes',
    'verified_by',
    'verified_at',
];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethods::class, 'payment_method_id');
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function photoReceipt()
    {
        return $this->hasOne(PhotoReceipt::class);
    }

    public function verifiedByUser()
{
    return $this->belongsTo(User::class, 'verified_by');
}

public function returnIssue()
{
    return $this->belongsTo(\App\Models\ReturnIssue::class);
}

}