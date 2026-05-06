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
        $history = (int) $this->option('history') === 1;

        $trackers = Tracker::query()
            ->whereNotNull('traccar_device_id')
            ->get(['id', 'traccar_device_id']);

        if ($trackers->isEmpty()) {
            $this->warn('No trackers with traccar_device_id found.');
            return self::SUCCESS;
        }

        $positions = collect($traccar->positions());

        if ($positions->isEmpty()) {
            $this->warn('No positions returned from Traccar.');
            return self::SUCCESS;
        }

        $byDevice = $positions->keyBy(function ($position) {
            return (int) ($position['deviceId'] ?? 0);
        });

        $saved = 0;
        $skipped = 0;

        foreach ($trackers as $tracker) {
            $deviceId = (int) $tracker->traccar_device_id;

            $p = $byDevice->get($deviceId);

            if (!$p) {
                $skipped++;
                continue;
            }

            $attrs = $p['attributes'] ?? [];

            $speedKn = (float) ($p['speed'] ?? 0);
            $speedKmh = $speedKn * 1.852;

            $payload = [
                'tracker_id' => $tracker->id,
                'traccar_position_id' => $p['id'] ?? null,
                'traccar_device_id' => $p['deviceId'] ?? null,
                'protocol' => $p['protocol'] ?? null,

                /*
                 * Traccar usually returns time in UTC.
                 * Convert it to the app timezone before saving so the live map
                 * online/offline check matches Asia/Manila time in cloud.
                 */
                'server_time' => $this->parseTraccarTime($p['serverTime'] ?? null),
                'device_time' => $this->parseTraccarTime($p['deviceTime'] ?? null),
                'fix_time' => $this->parseTraccarTime($p['fixTime'] ?? null),

                'latitude' => $p['latitude'] ?? null,
                'longitude' => $p['longitude'] ?? null,
                'altitude_m' => $p['altitude'] ?? null,

                'speed_kmh' => $speedKmh,
                'address' => $p['address'] ?? null,

                'activity' => $attrs['activity'] ?? null,
                'battery_level' => $attrs['batteryLevel'] ?? null,

                'odometer_km' => $attrs['odometer'] ?? null,

                'geofence_ids' => $p['geofenceIds'] ?? null,

                'distance_km' => $attrs['distance'] ?? null,
                'total_distance_km' => $attrs['totalDistance'] ?? null,

                'attributes' => $attrs,
            ];

            if ($history) {
                TrackerPosition::create($payload);
            } else {
                /*
                 * Latest-only mode:
                 * Keep one latest position row per tracker.
                 * This is best for the live map.
                 */
                TrackerPosition::updateOrCreate(
                    ['tracker_id' => $tracker->id],
                    $payload
                );
            }

            $saved++;
        }

        $this->info("Synced positions saved: {$saved}");
        $this->info("Trackers without matching Traccar position: {$skipped}");

        return self::SUCCESS;
    }

    private function parseTraccarTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)
            ->timezone(config('app.timezone', 'Asia/Manila'));
    }
}