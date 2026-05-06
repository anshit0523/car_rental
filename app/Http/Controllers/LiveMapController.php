<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Services\TraccarService;

class LiveMapController extends Controller
{
    public function page()
    {
        return view('admin.adminmap');
    }

    public function replayPage()
    {
        $cars = Car::query()
            ->whereNotNull('tracker_id')
            ->with('tracker')
            ->get(['id', 'model', 'tracker_id']);

        return view('admin.adminreplay', compact('cars'));
    }
public function positions()
{
    $cars = Car::query()
        ->whereNotNull('tracker_id')
        ->with(['tracker.latestPosition'])
        ->get(['id', 'model', 'tracker_id']);

    $now = Carbon::now('UTC');

    $data = $cars->map(function ($car) use ($now) {
        $tracker = $car->tracker;
        $position = $tracker?->latestPosition;

        $fixTime = $position?->fix_time
            ? Carbon::parse($position->fix_time)->utc()
            : null;

        // Online if latest GPS update is within 5 minutes
        $online = $fixTime
            ? $fixTime->greaterThanOrEqualTo($now->copy()->subMinutes(1))
            : false;

        return [
            'car_id' => $car->id,
            'plate_no' => $car->model,
            'tracker_id' => $tracker?->id,
            'traccar_device_id' => $tracker?->traccar_device_id,
            'online' => $online,
            'lat' => $position?->latitude,
            'lng' => $position?->longitude,
            'speed_kmh' => $position?->speed_kmh ?? 0,
            'battery_level' => $position?->battery_level,
            'odometer_km' => $position?->odometer_km,
            'geofence_ids' => $position?->geofence_ids ?? [],
            'fix_time' => $fixTime?->toIso8601String(),
            'address' => $position?->address,
        ];
    })->values();

    return response()->json([
        'updated_at' => Carbon::now('UTC')->toIso8601String(),
        'data' => $data,
    ]);
}

    public function history(Request $request, TraccarService $traccarService)
    {
        $validated = $request->validate([
            'deviceId' => ['required', 'integer'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $positions = $traccarService->positionHistory(
            (int) $validated['deviceId'],
            $validated['from'],
            $validated['to']
        );

        $data = collect($positions)->map(function ($position) {
            $latitude = $position['latitude'] ?? null;
            $longitude = $position['longitude'] ?? null;

            return [
                'id' => $position['id'] ?? null,
                'lat' => $latitude,
                'lng' => $longitude,
                'speed' => $position['speed'] ?? 0,
                'course' => $position['course'] ?? 0,
                'fixTime' => $position['fixTime'] ?? null,
                'address' => $position['address'] ?? (
                    isset($latitude, $longitude)
                        ? $latitude . ', ' . $longitude
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