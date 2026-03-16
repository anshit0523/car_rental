<?php

namespace App\Models;

use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
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
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relationships
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
}
