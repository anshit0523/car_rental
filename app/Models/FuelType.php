<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelType extends Model
{
     use HasFactory;

    protected $fillable = ['type'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
