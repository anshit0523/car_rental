<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'car_type_id',
        'transmission_id',
        'fuel_type_id',
        'model',
        'plate_number',
        'seats',
        'price_per_day',
        'description',
        'images',
        'active',
        'tracker_id',
    ];

    protected $casts = [
        'images' => 'array',
        'active' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function carType()
    {
        return $this->belongsTo(CarType::class);
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

   public function tracker()
{
    return $this->belongsTo(\App\Models\Tracker::class);
}

    public function scopeActive(Builder $query): Builder
{
    return $query->where('active', true);
}

public function scopeFilterLocation(Builder $query, $location): Builder
{
    if (!empty($location)) {
        $query->where('location', 'like', '%' . $location . '%');
    }

    return $query;
}

public function scopeFilterPrice(Builder $query, $minPrice, $maxPrice): Builder
{
    if ($minPrice !== null && $minPrice !== '' && $maxPrice !== null && $maxPrice !== '') {
        $query->whereBetween('price_per_day', [$minPrice, $maxPrice]);
    } elseif ($minPrice !== null && $minPrice !== '') {
        $query->where('price_per_day', '>=', $minPrice);
    } elseif ($maxPrice !== null && $maxPrice !== '') {
        $query->where('price_per_day', '<=', $maxPrice);
    }

    return $query;
}

public function scopeFilterBrand(Builder $query, $brandIds): Builder
{
    if (!empty($brandIds)) {
        $query->whereIn('brand_id', (array) $brandIds);
    }

    return $query;
}

public function scopeFilterFuelType(Builder $query, $fuelIds): Builder
{
    if (!empty($fuelIds)) {
        $query->whereIn('fuel_type_id', (array) $fuelIds);
    }

    return $query;
}

public function scopeFilterTransmission(Builder $query, $transmissionIds): Builder
{
    if (!empty($transmissionIds)) {
        $query->whereIn('transmission_id', (array) $transmissionIds);
    }

    return $query;
}

public function scopeFilterSeats(Builder $query, $minSeats): Builder
{
    if (!empty($minSeats)) {
        $query->where('seats', '>=', $minSeats);
    }

    return $query;
}

public function scopeFilterCarType(Builder $query, $carTypeIds): Builder
{
    return filled($carTypeIds)
        ? $query->whereIn('car_type_id', (array) $carTypeIds)
        : $query;
}
}
