@extends('layouts.adminlayout')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-7xl mx-auto p-4">
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-xl font-semibold">Staff Live Map</h1>
        <div class="text-sm text-gray-600" id="lastUpdated">—</div>
    </div>

    <div id="map" style="height: calc(100vh - 180px);" class="rounded border"></div>
</div>

<script>
    const map = L.map('map').setView([14.5995, 120.9842], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const markers = new Map();
    let didAutoCenter = false;

    const urlParams = new URLSearchParams(window.location.search);
    const selectedDeviceId = urlParams.get('deviceId');

    function popupHtml(v) {
        return `
            <div style="font-size:12px">
                <b>${v.plate_no ?? 'Car'}</b><br>
                Status: ${v.online ? 'ONLINE' : 'OFFLINE'}<br>
                Speed: ${(v.speed_kmh ?? 0)} km/h<br>
                Battery: ${v.battery_level ?? '-'}%<br>
                Fix: ${v.fix_time ?? '-'}<br><br>
                ${v.address ? `Address: ${v.address}` : ''}
                 <a href="{{ route('staff.replay') }}?deviceId=${v.traccar_device_id}"
               style="display:inline-block;padding:6px 10px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;font-size:12px;">
               Go to Replay
            </a>
            </div>
        `;
    }

    async function refresh() {
        try {
            const res = await fetch('/staff/live/positions', {
                headers: { 'Accept': 'application/json' }
            });

            const json = await res.json();

            const bounds = L.latLngBounds();
            let hasAny = false;
            let selectedMarker = null;

            (json.data || []).forEach(v => {
                const lat = Number(v.lat);
                const lng = Number(v.lng);

                if (isNaN(lat) || isNaN(lng)) return;

                hasAny = true;
                const pos = [lat, lng];
                bounds.extend(pos);

                let m;
                if (!markers.has(v.car_id)) {
                    m = L.marker(pos).addTo(map);
                    m.bindPopup(popupHtml(v));
                    markers.set(v.car_id, m);
                } else {
                    m = markers.get(v.car_id);
                    m.setLatLng(pos);
                    m.setPopupContent(popupHtml(v));
                }

                if (selectedDeviceId && String(v.traccar_device_id) === String(selectedDeviceId)) {
                    selectedMarker = m;
                }
            });

            if (json.updated_at) {
                document.getElementById('lastUpdated').textContent =
                    `Updated: ${new Date(json.updated_at).toLocaleString()}`;
            }

            if (!didAutoCenter) {
                if (selectedMarker) {
                    map.setView(selectedMarker.getLatLng(), 16);
                    selectedMarker.openPopup();
                    didAutoCenter = true;
                } else if (hasAny) {
                    map.fitBounds(bounds, { padding: [40, 40] });
                    didAutoCenter = true;
                }
            }
        } catch (error) {
            console.error('Failed to refresh live map:', error);
            document.getElementById('lastUpdated').textContent = 'Failed to load positions';
        }
    }

    refresh();
    setInterval(refresh, 5000);
</script>
@endsection