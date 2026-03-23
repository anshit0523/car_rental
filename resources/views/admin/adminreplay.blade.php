@extends('layouts.adminlayout')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="h-screen overflow-y-auto bg-gray-50">
    <div class="max-w-7xl mx-auto p-4 pb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Replay History</h1>
                <p class="text-sm text-gray-500">View past routes and replay vehicle movement.</p>
            </div>
            <div id="historyStatus" class="text-sm text-gray-500">Ready</div>
        </div>

        {{-- Map on top --}}
        <div id="map" class="h-[78vh] rounded-xl border border-gray-200 shadow-sm mb-4"></div>

        {{-- Controls below map --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle</label>
                    <select id="deviceSelect" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select vehicle</option>
                        @foreach($cars as $car)
                            @if($car->tracker && $car->tracker->traccar_device_id)
                                <option value="{{ $car->tracker->traccar_device_id }}">
                                    {{ $car->model }} (Device ID: {{ $car->tracker->traccar_device_id }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input
                        id="fromTime"
                        type="datetime-local"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input
                        id="toTime"
                        type="datetime-local"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div class="flex items-end gap-2">
                    <button
                        id="loadReplay"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Load
                    </button>

                    <button
                        id="playReplay"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Play
                    </button>

                    <button
                        id="stopReplay"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                    >
                        Stop
                    </button>
                </div>
            </div>

            <div class="mt-4 grid md:grid-cols-4 gap-4 text-sm">
                <div class="rounded-lg border border-gray-200 p-3 bg-gray-50">
                    <div class="text-gray-500">Points</div>
                    <div id="pointCount" class="text-lg font-semibold text-gray-900">0</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 bg-gray-50">
                    <div class="text-gray-500">Current Time</div>
                    <div id="currentFixTime" class="text-sm font-medium text-gray-900">—</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 bg-gray-50">
                    <div class="text-gray-500">Speed</div>
                    <div id="currentSpeed" class="text-lg font-semibold text-gray-900">0</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 bg-gray-50">
                    <div class="text-gray-500">Address</div>
                    <div id="currentAddress" class="text-sm font-medium text-gray-900 truncate">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const map = L.map('map').setView([14.5995, 120.9842], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    let replayPoints = [];
    let replayLine = null;
    let replayMarker = null;
    let replayTimer = null;
    let replayIndex = 0;
    let startMarker = null;
    let endMarker = null;

    const statusEl = document.getElementById('historyStatus');
    const pointCountEl = document.getElementById('pointCount');
    const currentFixTimeEl = document.getElementById('currentFixTime');
    const currentSpeedEl = document.getElementById('currentSpeed');
    const currentAddressEl = document.getElementById('currentAddress');

    function toDatetimeLocal(date) {
        const pad = (n) => String(n).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    function setDefaultTimeRange() {
        const now = new Date();
        const sixHoursAgo = new Date(now.getTime() - (6 * 60 * 60 * 1000));

        document.getElementById('fromTime').value = toDatetimeLocal(sixHoursAgo);
        document.getElementById('toTime').value = toDatetimeLocal(now);
    }

    function clearReplayLayers() {
        if (replayLine) {
            map.removeLayer(replayLine);
            replayLine = null;
        }

        if (replayMarker) {
            map.removeLayer(replayMarker);
            replayMarker = null;
        }

        if (startMarker) {
            map.removeLayer(startMarker);
            startMarker = null;
        }

        if (endMarker) {
            map.removeLayer(endMarker);
            endMarker = null;
        }

        if (replayTimer) {
            clearInterval(replayTimer);
            replayTimer = null;
        }

        replayPoints = [];
        replayIndex = 0;
        pointCountEl.textContent = '0';
        currentFixTimeEl.textContent = '—';
        currentSpeedEl.textContent = '0';
        currentAddressEl.textContent = '—';
    }

    function updateInfo(point) {
        currentFixTimeEl.textContent = point.fixTime ?? '—';
        currentSpeedEl.textContent = point.speed ?? 0;
        currentAddressEl.textContent = point.address ?? '—';
    }

    async function loadReplay() {
        const deviceId = document.getElementById('deviceSelect').value;
        const from = document.getElementById('fromTime').value;
        const to = document.getElementById('toTime').value;

        if (!deviceId) {
            alert('Please select a vehicle.');
            return;
        }

        if (!from || !to) {
            alert('Please select a valid date range.');
            return;
        }

        clearReplayLayers();
        statusEl.textContent = 'Loading history...';

        try {
            const fromIso = new Date(from).toISOString();
            const toIso = new Date(to).toISOString();

            const res = await fetch(`{{ route('admin.replay.history') }}?deviceId=${encodeURIComponent(deviceId)}&from=${encodeURIComponent(fromIso)}&to=${encodeURIComponent(toIso)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                throw new Error('Failed to load history.');
            }

            const json = await res.json();

            replayPoints = (json.data || []).filter(point =>
                point.lat !== null &&
                point.lng !== null &&
                point.lat !== undefined &&
                point.lng !== undefined
            );

            pointCountEl.textContent = replayPoints.length;

            if (!replayPoints.length) {
                statusEl.textContent = 'No history found for the selected range.';
                return;
            }

            const latlngs = replayPoints.map(point => [Number(point.lat), Number(point.lng)]);

            replayLine = L.polyline(latlngs, { weight: 4 }).addTo(map);

            startMarker = L.marker(latlngs[0]).addTo(map)
                .bindPopup(`Start<br>${replayPoints[0].fixTime ?? '-'}`);

            endMarker = L.marker(latlngs[latlngs.length - 1]).addTo(map)
                .bindPopup(`End<br>${replayPoints[replayPoints.length - 1].fixTime ?? '-'}`);

            replayMarker = L.marker(latlngs[0]).addTo(map)
                .bindPopup(`
                    <div style="font-size:12px">
                        <b>Replay</b><br>
                        Time: ${replayPoints[0].fixTime ?? '-'}<br>
                        Speed: ${replayPoints[0].speed ?? 0}<br>
                        Address: ${replayPoints[0].address ?? '-'}
                    </div>
                `);

            updateInfo(replayPoints[0]);

            map.fitBounds(replayLine.getBounds(), { padding: [30, 30] });
            statusEl.textContent = `Loaded ${replayPoints.length} points`;
        } catch (error) {
            console.error(error);
            statusEl.textContent = 'Error loading history';
            alert(error.message || 'Something went wrong while loading history.');
        }
    }

    function playReplay() {
        if (!replayPoints.length || !replayMarker) {
            alert('Load history first.');
            return;
        }

        if (replayTimer) {
            clearInterval(replayTimer);
        }

        statusEl.textContent = 'Playing replay...';

        replayTimer = setInterval(() => {
            if (replayIndex >= replayPoints.length) {
                clearInterval(replayTimer);
                replayTimer = null;
                statusEl.textContent = 'Replay finished';
                return;
            }

            const point = replayPoints[replayIndex];
            const pos = [Number(point.lat), Number(point.lng)];

            replayMarker.setLatLng(pos);
            replayMarker.setPopupContent(`
                <div style="font-size:12px">
                    <b>Replay</b><br>
                    Time: ${point.fixTime ?? '-'}<br>
                    Speed: ${point.speed ?? 0}<br>
                    Address: ${point.address ?? '-'}
                </div>
            `);

            updateInfo(point);
            replayIndex++;
        }, 500);
    }

    function stopReplay() {
        if (replayTimer) {
            clearInterval(replayTimer);
            replayTimer = null;
        }

        replayIndex = 0;

        if (replayPoints.length && replayMarker) {
            const firstPoint = replayPoints[0];
            replayMarker.setLatLng([Number(firstPoint.lat), Number(firstPoint.lng)]);
            replayMarker.setPopupContent(`
                <div style="font-size:12px">
                    <b>Replay</b><br>
                    Time: ${firstPoint.fixTime ?? '-'}<br>
                    Speed: ${firstPoint.speed ?? 0}<br>
                    Address: ${firstPoint.address ?? '-'}
                </div>
            `);

            updateInfo(firstPoint);
        }

        statusEl.textContent = 'Replay stopped';
    }

    document.getElementById('loadReplay').addEventListener('click', loadReplay);
    document.getElementById('playReplay').addEventListener('click', playReplay);
    document.getElementById('stopReplay').addEventListener('click', stopReplay);

    setDefaultTimeRange();

    @if(request('deviceId'))
        document.getElementById('deviceSelect').value = "{{ request('deviceId') }}";
    @endif
</script>
@endsection