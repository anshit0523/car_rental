@extends('layouts.adminlayout')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
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
        width: 38px;
        height: 38px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #111827;
        font-size: 16px;
        transition: 0.2s ease;
    }

    .replay-btn:hover {
        background: #f3f4f6;
    }

    .replay-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }
</style>

<div class="h-screen overflow-y-auto bg-gray-50">
    <div class="max-w-7xl mx-auto p-4 pb-8">

        <div class="mb-3 text-sm text-gray-600">
    <a href="{{ route('admin.live-map') }}" class="hover:text-blue-600">Live Map</a>
    <span class="mx-1">/</span>
    <span class="text-gray-900 font-medium">Replay History</span>
</div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Replay History</h1>
                <p class="text-sm text-gray-500">View past routes and replay vehicle movement.</p>
            </div>
            <div id="historyStatus" class="text-sm text-gray-500">Ready</div>
        </div>

        {{-- Map --}}
        <div id="map" class="h-[68vh] rounded-xl border border-gray-200 shadow-sm mb-4"></div>

        {{-- Replay Player --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="px-1 py-1">
                <div id="replayDeviceName" class="text-center text-xl md:text-1xl font-semibold text-gray-900">
                    No vehicle selected
                </div>

                <div class="mt-2">
                    <input id="replaySeek" type="range" min="0" max="0" value="0" disabled>
                </div>

                <div class="mt-2 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div class="flex flex-wrap items-center gap-1 md:gap-2">
                        <div id="replayCounter" class="text-gray-900 text-1xl md:text-1xl font-semibold tracking-tight mr-1">
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

                    <div id="replayCurrentTime" class="text-gray-900 text-lg md:text-1xl font-medium">
                        —
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="grid md:grid-cols-4 gap-4">
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
                        class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Load Replay
                    </button>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="mt-4 grid md:grid-cols-4 gap-4 text-sm">
            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Points</div>
                <div id="pointCount" class="text-lg font-semibold text-gray-900">0</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Current Time</div>
                <div id="currentFixTime" class="text-sm font-medium text-gray-900">—</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Speed</div>
                <div id="currentSpeed" class="text-lg font-semibold text-gray-900">0</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 bg-white shadow-sm">
                <div class="text-gray-500">Address</div>
                <div id="currentAddress" class="text-sm font-medium text-gray-900 truncate">—</div>
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

    // optional follow mode while replay plays
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
            month: '2-digit',
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
        currentSpeedEl.textContent = point.speed ?? 0;
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
            <div style="font-size:12px">
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
        currentSpeedEl.textContent = '0';
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

            const res = await fetch(`{{ route('admin.replay.history') }}?deviceId=${encodeURIComponent(deviceId)}&from=${encodeURIComponent(fromIso)}&to=${encodeURIComponent(toIso)}`, {
                headers: { 'Accept': 'application/json' }
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
                replayDeviceNameEl.textContent = selectedOption?.dataset?.label || 'Vehicle';
                return;
            }

            const latlngs = replayPoints.map(point => [Number(point.lat), Number(point.lng)]);

            replayLine = L.polyline(latlngs, { weight: 4 }).addTo(map);

            startMarker = L.marker(latlngs[0]).addTo(map)
                .bindPopup(`Start<br>${formatFixTime(replayPoints[0].fixTime)}`);

            endMarker = L.marker(latlngs[latlngs.length - 1]).addTo(map)
                .bindPopup(`End<br>${formatFixTime(replayPoints[replayPoints.length - 1].fixTime)}`);

            replayMarker = L.marker(latlngs[0]).addTo(map).bindPopup(`
    <div style="font-size:12px">
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

            // auto-center
            renderPoint(0, false);
            map.setView(latlngs[0], 16);

            // auto-open popup for replay marker
            replayMarker.openPopup();

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

    // auto-load from deviceId
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