<?php

namespace App\Models;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'pickup_at',
        'return_at',
        'total_price',
        'status_id',
        'payment_id',
        'service_type_id',
        'service_location',
        'car_id',
        'user_id',
        'pickup_at',
        'return_at',
        'total_price',
        'status_id',
        'service_type_id',
        'service_location',
        'points_used',
        'discount_amount',
        'final_total'
    ];

    protected $casts = [
        'pickup_at' => 'datetime',
        'return_at' => 'datetime',
    ];
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

public function photoReceipt()
{
    return $this->hasOne(\App\Models\PhotoReceipt::class);
}
public function notifications()
{
    return $this->hasMany(Notification::class);
}

public function serviceType()
{
    return $this->belongsTo(ServiceType::class);
}
}
