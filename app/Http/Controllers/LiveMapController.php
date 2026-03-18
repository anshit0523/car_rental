<?php

namespace App\Http\Controllers;

use App\Models\Car;

class LiveMapController extends Controller
{
    public function page()
    {
        return view('admin.adminmap');
    }

    public function positions()
    {
        $cars = Car::query()
            ->whereNotNull('tracker_id')
            ->with(['tracker.latestPosition'])
            ->get(['id', 'model', 'tracker_id']);

        $now = now();

        $data = $cars->map(function ($car) use ($now) {
            $t = $car->tracker;
            $p = $t?->latestPosition;

            $fix = $p?->fix_time;
            $online = $fix ? $fix->diffInSeconds($now) <= 120 : false;

            return [
                'car_id' => $car->id,
                'plate_no' => $car->model, // ✅ changed
                'tracker_id' => $t?->id,
                'online' => $online,

                'lat' => $p?->latitude,
                'lng' => $p?->longitude,
                'speed_kmh' => $p?->speed_kmh ?? 0,

                'battery_level' => $p?->battery_level,
                'odometer_km' => $p?->odometer_km,
                'geofence_ids' => $p?->geofence_ids ?? [],

                'fix_time' => $fix?->toIso8601String(),
                'address' => $p?->address,
            ];
        })->values();

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'data' => $data,
        ]);
    }
}