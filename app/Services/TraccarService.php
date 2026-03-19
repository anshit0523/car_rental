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
}