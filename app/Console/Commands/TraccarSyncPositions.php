<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use App\Models\TrackerPosition;
use App\Services\TraccarService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TraccarSyncPositions extends Command
{
    protected $signature = 'traccar:sync-positions {--history=0 : 1 = insert history rows, 0 = update latest only}';
    protected $description = 'Sync latest Traccar positions into tracker_positions';

    public function handle(TraccarService $traccar): int
    {
        $history = (int)$this->option('history') === 1;

        // Load trackers that have traccar_device_id
        $trackers = Tracker::query()
            ->whereNotNull('traccar_device_id')
            ->get(['id', 'traccar_device_id']);

        if ($trackers->isEmpty()) {
            $this->warn('No trackers with traccar_device_id found.');
            return self::SUCCESS;
        }

        $positions = collect($traccar->positions());
        $byDevice = $positions->keyBy('deviceId');

        $saved = 0;

        foreach ($trackers as $tracker) {
            $p = $byDevice->get((int)$tracker->traccar_device_id);
            if (!$p) continue;

            $attrs = $p['attributes'] ?? [];

            $speedKn = (float)($p['speed'] ?? 0);
            $speedKmh = $speedKn * 1.852;

            $payload = [
                'tracker_id' => $tracker->id,
                'traccar_position_id' => $p['id'] ?? null,
                'traccar_device_id' => $p['deviceId'] ?? null,
                'protocol' => $p['protocol'] ?? null,

                'server_time' => isset($p['serverTime']) ? Carbon::parse($p['serverTime']) : null,
                'device_time' => isset($p['deviceTime']) ? Carbon::parse($p['deviceTime']) : null,
                'fix_time'    => isset($p['fixTime']) ? Carbon::parse($p['fixTime']) : null,

                'latitude' => $p['latitude'] ?? null,
                'longitude' => $p['longitude'] ?? null,
                'altitude_m' => $p['altitude'] ?? null,

                'speed_kmh' => $speedKmh,
                'address' => $p['address'] ?? null,

                'activity' => $attrs['activity'] ?? null,
                'battery_level' => $attrs['batteryLevel'] ?? null,

                // depends on your Traccar/device; keep it nullable
                'odometer_km' => $attrs['odometer'] ?? null,

                // geofenceIds is usually top-level array
                'geofence_ids' => $p['geofenceIds'] ?? null,

                'distance_km' => $attrs['distance'] ?? null,
                'total_distance_km' => $attrs['totalDistance'] ?? null,

                'attributes' => $attrs,
            ];

            if ($history) {
                TrackerPosition::create($payload);
                $saved++;
            } else {
                // latest-only mode: keep 1 row per tracker (overwrite)
                TrackerPosition::updateOrCreate(
                    ['tracker_id' => $tracker->id],
                    $payload
                );
                $saved++;
            }
        }

        $this->info("Synced positions saved: {$saved}");
        return self::SUCCESS;
    }
}