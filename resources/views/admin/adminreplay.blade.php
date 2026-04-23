@extends('layouts.adminlayout')

@section('content')
@php
    $routePrefix = request()->routeIs('manager.*') ? 'manager' : 'admin';

    $liveMapUrl = route($routePrefix . '.live-map');
    $historyUrl = route($routePrefix . '.replay.history');
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map {
        width: 100%;
        height: 68vh;
        min-height: 340px;
        border-radius: 0.75rem;
        z-index: 1;
    }

    @media (max-width: 1024px) {
        #map {
            height: 62vh;
            min-height: 320px;
        }
    }

    @media (max-width: 768px) {
        #map {
            height: 54vh;
            min-height: 300px;
        }
    }

    @media (max-width: 480px) {
        #map {
            height: 48vh;
            min-height: 280px;
        }
    }

    #replaySeek {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 9999px;
        outline: none;
        background: linear-gradient(to right, #60a5fa 0%, #60a5fa 0%, #d1d5db 0%, #d1d5db 100%);
    }

    #replaySeek::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 9999px;
        background: #2563eb;
        border: 0;
        cursor: pointer;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    #replaySeek::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border: 0;
        border-radius: 9999px;
        background: #2563eb;
        cursor: pointer;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    .replay-btn {
        width: 40px;
        height: 40px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: #111827;
        border: 1px solid #e5e7eb;
        font-size: 16px;
        transition: 0.2s ease;
    }

    .replay-btn:hover {
        background: #f3f4f6;
    }

    .replay-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
</style>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto p-3 sm:p-4 pb-8">

        <div class="mb-3 text-sm text-gray-600">
            <a href="{{ $liveMapUrl }}" class="hover:text-blue-600">Live Map</a>
            <span class="mx-1">/</span>
            <span class="text-gray-900 font-medium">Replay History</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Replay History</h1>
                <p class="text-sm text-gray-500">View past routes and replay vehicle movement.</p>
            </div>
            <div id="historyStatus" class="text-sm text-gray-500">Ready</div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-4">
            <div id="map"></div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="px-1 py-1">
                <div id="replayDeviceName" class="text-center text-lg sm:text-xl font-semibold text-gray-900">
                    No vehicle selected
                </div>

                <div class="mt-3">
                    <input id="replaySeek" type="range" min="0" max="0" value="0" disabled>
                </div>

                <div class="mt-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <div id="replayCounter" class="text-gray-900 text-lg font-semibold tracking-tight mr-1">
                            0/0
                        </div>

                        <button id="prevPoint" type="button" class="replay-btn" title="Previous point" disabled>
                            &#9194;
                        </button>

                        <button id="playReplay" type="button" class="replay-btn" title="Play" disabled>
                            &#9654;
                        </button>

                        <button id="nextPoint" type="button" class="replay-btn" title="Next point" disabled>
                            &#9193;
                        </button>

                        <button id="stopReplay" type="button" class="replay-btn" title="Stop" disabled>
                            &#9632;
                        </button>
                    </div>

                    <div id="replayCurrentTime" class="text-gray-900 text-sm sm:text-base font-medium break-all">
                        —
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle</label>
                    <select id="deviceSelect" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select vehicle</option>
                        @foreach($cars as $car)
                            @if($car->tracker && $car->tracker->traccar_device_id)
                                <option
                                    value="{{ $car->tracker->traccar_device_id }}"
                                    data-label="{{ $car->model }}"
                                >
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

                <div class="flex items-end">
                    <button
                        id="loadReplay"
                        type="button"
                        class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Load Replay
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 text-sm">
            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Points</div>
                <div id="pointCount" class="text-lg font-semibold text-gray-900">0</div>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Current Time</div>
                <div id="currentFixTime" class="text-sm font-medium text-gray-900 break-words">—</div>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Speed</div>
                <div id="currentSpeed" class="text-lg font-semibold text-gray-900">0 km/h</div>
            </div>

            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Address</div>
                <div id="currentAddress" class="text-sm font-medium text-gray-900 break-words">—</div>
            </div>
        </div>
    </div>
</div>

<script>
    const historyUrl = @json($historyUrl);

    const map = L.map('map', {
        zoomControl: true
    }).setView([14.5995, 120.9842], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let replayPoints = [];
    let replayLine = null;
    let replayMarker = null;
    let replayTimer = null;
    let replayIndex = 0;
    let startMarker = null;
    let endMarker = null;
    let followMarker = true;

    const statusEl = document.getElementById('historyStatus');
    const pointCountEl = document.getElementById('pointCount');
    const currentFixTimeEl = document.getElementById('currentFixTime');
    const currentSpeedEl = document.getElementById('currentSpeed');
    const currentAddressEl = document.getElementById('currentAddress');

    const replaySeekEl = document.getElementById('replaySeek');
    const replayCounterEl = document.getElementById('replayCounter');
    const replayCurrentTimeEl = document.getElementById('replayCurrentTime');
    const replayDeviceNameEl = document.getElementById('replayDeviceName');

    const playReplayBtn = document.getElementById('playReplay');
    const stopReplayBtn = document.getElementById('stopReplay');
    const prevPointBtn = document.getElementById('prevPoint');
    const nextPointBtn = document.getElementById('nextPoint');

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

    function formatFixTime(value) {
        if (!value) return '—';

        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return date.toLocaleString([], {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
    }

    function updateSliderProgress() {
        const max = Number(replaySeekEl.max) || 0;
        const value = Number(replaySeekEl.value) || 0;
        const percent = max === 0 ? 0 : (value / max) * 100;

        replaySeekEl.style.background =
            `linear-gradient(to right, #60a5fa 0%, #60a5fa ${percent}%, #d1d5db ${percent}%, #d1d5db 100%)`;
    }

    function setPlaybackControlsDisabled(disabled) {
        replaySeekEl.disabled = disabled;
        playReplayBtn.disabled = disabled;
        stopReplayBtn.disabled = disabled;
        prevPointBtn.disabled = disabled;
        nextPointBtn.disabled = disabled;
    }

    function updateCounter() {
        replayCounterEl.textContent = replayPoints.length
            ? `${replayIndex + 1}/${replayPoints.length}`
            : '0/0';
    }

    function updatePlayButton(isPlaying = false) {
        playReplayBtn.innerHTML = isPlaying ? '&#10074;&#10074;' : '&#9654;';
        playReplayBtn.title = isPlaying ? 'Pause' : 'Play';
    }

    function stopTimer() {
        if (replayTimer) {
            clearInterval(replayTimer);
            replayTimer = null;
        }
        updatePlayButton(false);
    }

    function updateInfo(point) {
        currentFixTimeEl.textContent = formatFixTime(point.fixTime);
        replayCurrentTimeEl.textContent = formatFixTime(point.fixTime);
        currentSpeedEl.textContent = `${point.speed ?? 0} km/h`;
        currentAddressEl.textContent = point.address ?? '—';
    }

    function renderPoint(index, panMap = true) {
        if (!replayPoints.length || !replayMarker) return;

        replayIndex = Math.max(0, Math.min(index, replayPoints.length - 1));

        const point = replayPoints[replayIndex];
        const pos = [Number(point.lat), Number(point.lng)];

        replaySeekEl.value = replayIndex;
        updateSliderProgress();
        updateCounter();
        updateInfo(point);

        replayMarker.setLatLng(pos);
        replayMarker.setPopupContent(`
            <div style="font-size:12px; line-height:1.5; min-width:180px;">
                <b>Replay</b><br>
                Time: ${formatFixTime(point.fixTime)}<br>
                Speed: ${point.speed ?? 0} km/h<br>
                Address: ${point.address ?? '-'}
            </div>
        `);

        if (panMap && followMarker) {
            map.panTo(pos, { animate: true, duration: 0.5 });
        }
    }

    function clearReplayLayers() {
        stopTimer();

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

        replayPoints = [];
        replayIndex = 0;

        pointCountEl.textContent = '0';
        currentFixTimeEl.textContent = '—';
        currentSpeedEl.textContent = '0 km/h';
        currentAddressEl.textContent = '—';
        replayCurrentTimeEl.textContent = '—';
        replayDeviceNameEl.textContent = 'No vehicle selected';

        replaySeekEl.max = 0;
        replaySeekEl.value = 0;
        updateSliderProgress();
        updateCounter();
        setPlaybackControlsDisabled(true);
    }

    async function loadReplay() {
        const deviceSelect = document.getElementById('deviceSelect');
        const selectedOption = deviceSelect.options[deviceSelect.selectedIndex];
        const deviceId = deviceSelect.value;
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
        replayDeviceNameEl.textContent = selectedOption?.dataset?.label || 'Vehicle';

        try {
            const fromIso = new Date(from).toISOString();
            const toIso = new Date(to).toISOString();

            const res = await fetch(
                `${historyUrl}?deviceId=${encodeURIComponent(deviceId)}&from=${encodeURIComponent(fromIso)}&to=${encodeURIComponent(toIso)}`,
                {
                    headers: { 'Accept': 'application/json' }
                }
            );

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
                .bindPopup(`Start<br>${formatFixTime(replayPoints[0].fixTime)}`);

            endMarker = L.marker(latlngs[latlngs.length - 1]).addTo(map)
                .bindPopup(`End<br>${formatFixTime(replayPoints[replayPoints.length - 1].fixTime)}`);

            replayMarker = L.marker(latlngs[0]).addTo(map).bindPopup(`
                <div style="font-size:12px; line-height:1.5; min-width:180px;">
                    <b>Replay</b><br>
                    Time: ${formatFixTime(replayPoints[0].fixTime)}<br>
                    Speed: ${replayPoints[0].speed ?? 0} km/h<br>
                    Address: ${replayPoints[0].address ?? '-'}
                </div>
            `);

            replayIndex = 0;
            replaySeekEl.max = replayPoints.length - 1;
            replaySeekEl.value = 0;
            updateSliderProgress();
            updateCounter();
            setPlaybackControlsDisabled(false);

            renderPoint(0, false);
            map.fitBounds(L.latLngBounds(latlngs), { padding: [30, 30] });

            setTimeout(() => {
                replayMarker.openPopup();
                map.invalidateSize();
            }, 150);

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
            stopTimer();
            statusEl.textContent = 'Replay paused';
            return;
        }

        if (replayIndex >= replayPoints.length - 1) {
            replayIndex = 0;
            renderPoint(0, false);
        }

        updatePlayButton(true);
        statusEl.textContent = followMarker ? 'Playing replay with follow mode...' : 'Playing replay...';

        replayTimer = setInterval(() => {
            if (replayIndex >= replayPoints.length - 1) {
                stopTimer();
                statusEl.textContent = 'Replay finished';
                return;
            }

            renderPoint(replayIndex + 1, true);
        }, 500);
    }

    function stopReplay() {
        stopTimer();

        if (replayPoints.length) {
            renderPoint(0, false);
            map.setView([Number(replayPoints[0].lat), Number(replayPoints[0].lng)], 16);
            replayMarker.openPopup();
        }

        statusEl.textContent = 'Replay stopped';
    }

    function previousPoint() {
        if (!replayPoints.length) return;

        stopTimer();
        renderPoint(replayIndex - 1, true);
        replayMarker.openPopup();
        statusEl.textContent = 'Moved to previous point';
    }

    function nextPoint() {
        if (!replayPoints.length) return;

        stopTimer();
        renderPoint(replayIndex + 1, true);
        replayMarker.openPopup();
        statusEl.textContent = 'Moved to next point';
    }

    document.getElementById('loadReplay').addEventListener('click', loadReplay);
    playReplayBtn.addEventListener('click', playReplay);
    stopReplayBtn.addEventListener('click', stopReplay);
    prevPointBtn.addEventListener('click', previousPoint);
    nextPointBtn.addEventListener('click', nextPoint);

    replaySeekEl.addEventListener('input', function () {
        if (!replayPoints.length) return;

        stopTimer();
        renderPoint(Number(this.value), true);
        replayMarker.openPopup();
        statusEl.textContent = 'Replay position updated';
    });

    setDefaultTimeRange();
    updateSliderProgress();
    setPlaybackControlsDisabled(true);

    window.addEventListener('load', () => {
        setTimeout(() => map.invalidateSize(), 300);
    });

    window.addEventListener('resize', () => {
        setTimeout(() => map.invalidateSize(), 150);
    });

    @if(request('deviceId'))
        document.getElementById('deviceSelect').value = "{{ request('deviceId') }}";

        window.addEventListener('load', () => {
            setTimeout(() => {
                loadReplay();
            }, 300);
        });
    @endif
</script>
@endsection