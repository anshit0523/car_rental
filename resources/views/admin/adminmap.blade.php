@extends('layouts.adminlayout')

@section('content')
@php
    $isManager = request()->routeIs('manager.*');

    $positionsUrl = $isManager
        ? route('manager.live.positions')
        : route('admin.live.positions');

    $replayBaseUrl = $isManager
        ? route('manager.replay')
        : route('admin.replay');
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map {
        width: 100%;
        height: calc(100vh - 180px);
        min-height: 420px;
        border-radius: 0.75rem;
        z-index: 1;
    }

    @media (max-width: 1024px) {
        #map {
            height: calc(100vh - 220px);
            min-height: 400px;
        }
    }

    @media (max-width: 768px) {
        #map {
            height: 65vh;
            min-height: 360px;
        }
    }

    @media (max-width: 480px) {
        #map {
            height: 60vh;
            min-height: 320px;
        }
    }
</style>

<div class="max-w-7xl mx-auto p-3 sm:p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
        <h1 class="text-lg sm:text-xl font-semibold">Live Map</h1>
        <div class="text-xs sm:text-sm text-gray-600" id="lastUpdated">Last updated: —</div>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <div id="map"></div>
    </div>
</div>

<script>
    const positionsUrl = @json($positionsUrl);
    const replayBaseUrl = @json($replayBaseUrl);

    const map = L.map('map', {
        zoomControl: true
    }).setView([14.5995, 120.9842], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = new Map();
    let didAutoCenter = false;
    let isRefreshing = false;

    const urlParams = new URLSearchParams(window.location.search);
    const selectedDeviceId = urlParams.get('deviceId');

    function formatDateTime(value) {
        if (!value) return '—';

        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return date.toLocaleString(undefined, {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function popupHtml(v) {
        const replayUrl = v.traccar_device_id
            ? `${replayBaseUrl}?deviceId=${encodeURIComponent(v.traccar_device_id)}`
            : null;

        return `
            <div style="font-size:12px; line-height:1.5; min-width:180px;">
                <b>${v.plate_no ?? 'Car'}</b><br>
                Status: ${v.online ? 'ONLINE' : 'OFFLINE'}<br>
                Speed: ${Number(v.speed_kmh ?? 0).toFixed(0)} km/h<br>
                Battery: ${v.battery_level ?? '-'}%<br>
                Fix: ${formatDateTime(v.fix_time)}<br><br>
                ${
                    replayUrl
                        ? `<a href="${replayUrl}"
                              style="display:inline-block;padding:6px 10px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;font-size:12px;">
                              Go to Replay
                           </a>`
                        : `<span style="color:#6b7280;">No tracker available</span>`
                }
            </div>
        `;
    }

    async function refresh() {
        if (isRefreshing) return;
        isRefreshing = true;

        try {
            const res = await fetch(positionsUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) {
                console.error('Failed to fetch positions:', res.status, res.statusText);
                return;
            }

            const json = await res.json();

            document.getElementById('lastUpdated').textContent =
                `Last updated: ${formatDateTime(json.updated_at)}`;

            const bounds = L.latLngBounds();
            let hasAny = false;
            let selectedMarker = null;

            const seenCarIds = new Set();

            (json.data || []).forEach(v => {
                if (
                    v.lat === null ||
                    v.lng === null ||
                    v.lat === undefined ||
                    v.lng === undefined ||
                    v.lat === '' ||
                    v.lng === ''
                ) {
                    return;
                }

                const lat = Number(v.lat);
                const lng = Number(v.lng);

                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    return;
                }

                seenCarIds.add(String(v.car_id));
                hasAny = true;

                const pos = [lat, lng];
                bounds.extend(pos);

                let marker;

                if (!markers.has(String(v.car_id))) {
                    marker = L.marker(pos).addTo(map);
                    marker.bindPopup(popupHtml(v));
                    markers.set(String(v.car_id), marker);
                } else {
                    marker = markers.get(String(v.car_id));
                    marker.setLatLng(pos);
                    marker.setPopupContent(popupHtml(v));
                }

                if (selectedDeviceId && String(v.traccar_device_id) === String(selectedDeviceId)) {
                    selectedMarker = marker;
                }
            });

            for (const [carId, marker] of markers.entries()) {
                if (!seenCarIds.has(String(carId))) {
                    map.removeLayer(marker);
                    markers.delete(String(carId));
                }
            }

            if (!didAutoCenter) {
                if (selectedMarker) {
                    map.setView(selectedMarker.getLatLng(), 16);
                    selectedMarker.openPopup();
                    didAutoCenter = true;
                } else if (hasAny) {
                    map.fitBounds(bounds, { padding: [30, 30] });
                    didAutoCenter = true;
                }
            }

            setTimeout(() => {
                map.invalidateSize();
            }, 150);

        } catch (error) {
            console.error('Map refresh error:', error);
        } finally {
            isRefreshing = false;
        }
    }

    refresh();
    setInterval(refresh, 4000);

    window.addEventListener('load', () => {
        setTimeout(() => map.invalidateSize(), 300);
    });

    window.addEventListener('resize', () => {
        setTimeout(() => map.invalidateSize(), 150);
    });
</script>
@endsection