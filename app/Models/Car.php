<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'transmission_id',
        'fuel_type_id',
        'model',
        'seats',
        'price_per_day',
        'description',
        'images',
        'active',
    ];

    protected $casts = [
        'images' => 'array',
        'active' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
   
   
    }

    
}
