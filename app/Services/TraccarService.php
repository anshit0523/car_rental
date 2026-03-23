<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TraccarService
{
    private function client()
    {
        return Http::withBasicAuth(
            config('services.traccar.user'),
            config('services.traccar.pass')
        )->baseUrl(rtrim(config('services.traccar.url'), '/'));
    }

    public function devices(): array
    {
        return $this->client()->get('/api/devices')->throw()->json();
    }

    public function positions(): array
    {
        return $this->client()->get('/api/positions')->throw()->json();
    }

    public function positionHistory(int $deviceId, string $from, string $to): array
{
    return $this->client()
        ->acceptJson()
        ->get('/api/positions', [
            'deviceId' => $deviceId,
            'from' => $from,
            'to' => $to,
        ])
        ->throw()
        ->json();
}

}