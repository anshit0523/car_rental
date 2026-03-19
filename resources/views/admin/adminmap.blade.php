@extends('layouts.adminlayout')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-7xl mx-auto p-4">
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-xl font-semibold">Live Map</h1>
    <div class="text-sm text-gray-600" id="lastUpdated">—</div>
  </div>

  <div id="map" style="height: calc(100vh - 180px);" class="rounded border"></div>
</div>

<script>
  const map = L.map('map').setView([14.5995, 120.9842], 11);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  const markers = new Map();
  let didAutoCenter = false;

  function popupHtml(v){
    return `
      <div style="font-size:12px">
        <b>${v.plate_no ?? 'Car'}</b><br>
        Status: ${v.online ? 'ONLINE' : 'OFFLINE'}<br>
        Speed: ${(v.speed_kmh ?? 0)} km/h<br>
        Battery: ${v.battery_level ?? '-'}%<br>
        Fix: ${v.fix_time ?? '-'}
      </div>
    `;
  }

  async function refresh(){
    const res = await fetch('/admin/live/positions', { headers: { 'Accept': 'application/json' } });
    const json = await res.json();

    const bounds = L.latLngBounds();
    let hasAny = false;

    (json.data || []).forEach(v => {
      const lat = Number(v.lat);
      const lng = Number(v.lng);
      if (!lat || !lng) return;

      hasAny = true;
      const pos = [lat, lng];
      bounds.extend(pos);

      if (!markers.has(v.car_id)) {
        const m = L.marker(pos).addTo(map);
        m.bindPopup(popupHtml(v));
        markers.set(v.car_id, m);
      } else {
        const m = markers.get(v.car_id);
        m.setLatLng(pos);
        m.setPopupContent(popupHtml(v));
      }
    });

    // ✅ Auto-center once (first time we have markers)
    if (!didAutoCenter && hasAny) {
      map.fitBounds(bounds, { padding: [40, 40] });
      didAutoCenter = true;
    }
  }

  refresh();
  setInterval(refresh, 5000);
</script>
@endsection