<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tracker;
use Illuminate\Http\Request;
use App\Services\TraccarService;

class AdminTrackerController extends Controller
{
    public function create()
    {
        $cars = Car::orderBy('model')->get(['id', 'model']);

        return view('admin.admincreatetracker', compact('cars'));
    }

    public function store(Request $request, TraccarService $traccarService)
    {
        $validated = $request->validate([
            'provider' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:100',
            'imei' => 'required|string|max:50|unique:trackers,imei',
            'sim_number' => 'nullable|string|max:30',
            'sim_network' => 'nullable|string|max:30',
            'apn' => 'nullable|string|max:100',
            'car_id' => 'nullable|exists:cars,id',
            'is_active' => 'nullable|boolean',
        ]);

        $imei = trim($validated['imei']);
        $devices = $traccarService->devices();
        $traccarDeviceId = null;

        foreach ($devices as $device) {
            $uniqueId = trim((string) ($device['uniqueId'] ?? ''));

            if ($uniqueId === $imei) {
                $traccarDeviceId = $device['id'];
                break;
            }
        }

        if (!$traccarDeviceId) {
            return back()
                ->withErrors([
                    'imei' => 'No matching device found in Traccar for this IMEI.'
                ])
                ->withInput();
        }

        $tracker = Tracker::create([
            'provider' => $validated['provider'] ?? 'sinotrack',
            'model' => $validated['model'] ?? null,
            'imei' => $imei,
            'traccar_device_id' => $traccarDeviceId,
            'sim_number' => $validated['sim_number'] ?? null,
            'sim_network' => $validated['sim_network'] ?? null,
            'apn' => $validated['apn'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($validated['car_id'])) {
            Car::where('id', $validated['car_id'])->update([
                'tracker_id' => $tracker->id
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Tracker added successfully!');
    }
}