<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class CarType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

}