<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Services\TraccarService;
use Carbon\Carbon;

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

public function positions(TraccarService $traccarService)
{
    $cars = Car::query()
        ->whereNotNull('tracker_id')
        ->with(['tracker.latestPosition'])
        ->get(['id', 'model', 'tracker_id']);

    $now = Carbon::now(config('app.timezone', 'Asia/Manila'));

    $devices = collect($traccarService->devices())
        ->keyBy(function ($device) {
            return (int) ($device['id'] ?? 0);
        });

    $data = $cars->map(function ($car) use ($now, $devices) {
        $tracker = $car->tracker;
        $position = $tracker?->latestPosition;

        $traccarDeviceId = (int) ($tracker?->traccar_device_id ?? 0);
        $device = $devices->get($traccarDeviceId);

        $traccarStatus = $device['status'] ?? null;

        $fixTime = $position?->fix_time
            ? Carbon::parse($position->fix_time)
            : null;

        $serverTime = $position?->server_time
            ? Carbon::parse($position->server_time)
            : null;

        $deviceTime = $position?->device_time
            ? Carbon::parse($position->device_time)
            : null;

        $updatedAt = $position?->updated_at
            ? Carbon::parse($position->updated_at)
            : null;

        $lastSeenTime = collect([
            $serverTime,
            $deviceTime,
            $fixTime,
        ])->filter()->sortByDesc(function ($time) {
            return $time->timestamp;
        })->first();

        $lastSeenTime = $lastSeenTime ?? $updatedAt;

        // Use Traccar device status first
        if ($traccarStatus) {
            $online = $traccarStatus === 'online';
        } else {
            $online = $lastSeenTime
                ? $lastSeenTime->greaterThanOrEqualTo($now->copy()->subMinutes(5))
                : false;
        }

        return [
            'car_id' => $car->id,
            'plate_no' => $car->model,
            'tracker_id' => $tracker?->id,
            'traccar_device_id' => $tracker?->traccar_device_id,
            'traccar_status' => $traccarStatus,
            'online' => $online,

            'lat' => $position?->latitude !== null ? (float) $position->latitude : null,
            'lng' => $position?->longitude !== null ? (float) $position->longitude : null,

            'speed_kmh' => $position?->speed_kmh ?? 0,
            'battery_level' => $position?->battery_level,
            'odometer_km' => $position?->odometer_km,
            'geofence_ids' => $position?->geofence_ids ?? [],

            'fix_time' => $fixTime?->toIso8601String(),
            'server_time' => $serverTime?->toIso8601String(),
            'device_time' => $deviceTime?->toIso8601String(),
            'last_seen_time' => $lastSeenTime?->toIso8601String(),

            'address' => $position?->address,
        ];
    })->values();

    return response()->json([
        'updated_at' => Carbon::now(config('app.timezone', 'Asia/Manila'))->toIso8601String(),
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