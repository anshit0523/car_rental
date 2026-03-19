<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tracker extends Model
{
    protected $fillable = [
        'provider',
        'model',
        'imei',
        'traccar_device_id',
        'sim_number',
        'sim_network',
        'apn',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function car(): HasOne
    {
        return $this->hasOne(Car::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(TrackerPosition::class);
    }

    // If you store history, this gives the latest row by fix_time
    public function latestPosition(): HasOne
    {
        return $this->hasOne(TrackerPosition::class)->latestOfMany('fix_time');
    }
}