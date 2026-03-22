<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tracker;
use Illuminate\Http\Request;

class AdminTrackerController extends Controller
{
    public function create()
    {
        // show cars for assignment dropdown
        $cars = \App\Models\Car::orderBy('model')->get(['id','model']);

       return view('admin.admincreatetracker', compact('cars'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:100',
            'imei' => 'required|string|max:50',                 // Traccar Client uniqueId / IMEI
            'traccar_device_id' => 'required|integer|min:1',    // Traccar internal device id (ex: 12560)

            'sim_number' => 'nullable|string|max:30',
            'sim_network' => 'nullable|string|max:30',
            'apn' => 'nullable|string|max:100',

            'car_id' => 'nullable|exists:cars,id',              // assign tracker to car
            'is_active' => 'nullable|boolean',
        ]);

        $tracker = Tracker::create([
            'provider' => $validated['provider'] ?? 'sinotrack',
            'model' => $validated['model'] ?? null,
            'imei' => $validated['imei'],
            'traccar_device_id' => $validated['traccar_device_id'],

            'sim_number' => $validated['sim_number'] ?? null,
            'sim_network' => $validated['sim_network'] ?? null,
            'apn' => $validated['apn'] ?? null,

            'is_active' => $request->has('is_active'),
        ]);

        // Optional: assign to a car
        if (!empty($validated['car_id'])) {
            Car::where('id', $validated['car_id'])->update([
                'tracker_id' => $tracker->id
            ]);
        }

        return redirect()
            ->route('admin.dashboard') // change to trackers index when you create it
            ->with('success', 'Tracker added successfully!');
    }
}