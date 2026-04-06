<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Services\TraccarService;

class StaffMapController extends Controller
{
    public function index()
    {
        return view('staff.staffmap');
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
                'plate_no' => $car->model,
                'tracker_id' => $t?->id,
                'traccar_device_id' => $t?->traccar_device_id,
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


    public function replayPage()
{
    $cars = \App\Models\Car::query()
        ->whereNotNull('tracker_id')
        ->with('tracker')
        ->get(['id', 'model', 'tracker_id']);

    return view('staff.staffreplay', compact('cars'));
}

public function history(\Illuminate\Http\Request $request, \App\Services\TraccarService $traccarService)
{
    $validated = $request->validate([
        'deviceId' => 'required|integer',
        'from' => 'required|date',
        'to' => 'required|date',
    ]);

    $positions = $traccarService->positionHistory(
        (int) $validated['deviceId'],
        $validated['from'],
        $validated['to']
    );

    $data = collect($positions)->map(function ($p) {
        return [
            'id' => $p['id'] ?? null,
            'lat' => $p['latitude'] ?? null,
            'lng' => $p['longitude'] ?? null,
            'speed' => $p['speed'] ?? 0,
            'course' => $p['course'] ?? 0,
            'fixTime' => $p['fixTime'] ?? null,
            'address' => $p['address'] ?? (
                isset($p['latitude'], $p['longitude'])
                    ? $p['latitude'] . ', ' . $p['longitude']
                    : null
            ),
        ];
    })->values();

    return response()->json([
        'count' => $data->count(),
        'data' => $data,
    ]);
}

}