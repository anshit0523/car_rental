<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackerPosition extends Model
{
    protected $fillable = [
        'tracker_id',
        'traccar_position_id',
        'traccar_device_id',
        'protocol',
        'server_time',
        'device_time',
        'fix_time',
        'latitude',
        'longitude',
        'altitude_m',
        'speed_kmh',
        'address',
        'activity',
        'battery_level',
        'odometer_km',
        'geofence_ids',
        'distance_km',
        'total_distance_km',
        'attributes',
    ];

    protected $casts = [
        'server_time' => 'datetime',
        'device_time' => 'datetime',
        'fix_time' => 'datetime',
        'attributes' => 'array',
        'geofence_ids' => 'array',
    ];

    public function tracker(): BelongsTo
    {
        return $this->belongsTo(Tracker::class);
    }
}