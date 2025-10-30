<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
