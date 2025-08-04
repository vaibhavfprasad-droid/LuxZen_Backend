@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h1>Driver Live Locations</h1>
    
    <!-- Map showing live driver positions -->
    <div id="map" style="width: 100%; height: 500px; border: 1px solid #ccc; margin-bottom: 2rem;"></div>

    <!-- The existing table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Driver ID</th>
                <th>Driver Name</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $location)
                <tr>
                    <td>{{ $location->driver_id }}</td>
                    <td>{{ $location->driver->name ?? 'N/A' }}</td>
                    <td>{{ number_format($location->latitude, 6) }}</td>
                    <td>{{ number_format($location->longitude, 6) }}</td>
                    <td>{{ optional($location->created_at)->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No driver locations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Google Maps JS API (uses key from env/config) -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap" async defer></script>

<script>
    let map;
    let markers = {};

    function initMap() {
        // Center on first driver or default
        @if(count($locations) > 0)
            const initialCenter = { lat: {{ $locations[0]->latitude }}, lng: {{ $locations[0]->longitude }} };
        @else
            const initialCenter = { lat: 20.0, lng: 75.0 };
        @endif

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: initialCenter,
        });

        // Place all markers
        updateMarkers(@json($locations));
    }

    // Reusable marker update function
    function updateMarkers(locations) {
        // Remove old markers
        for (const id in markers) {
            markers[id].setMap(null);
        }
        markers = {};

        locations.forEach(loc => {
            const lat = parseFloat(loc.latitude), lng = parseFloat(loc.longitude);
            if (isNaN(lat) || isNaN(lng)) return;
            const position = { lat, lng };

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: `${loc.driver?.name ? loc.driver.name : 'Driver #'+loc.driver_id}`,
                label: `${loc.driver_id}`,
            });

            markers[loc.driver_id] = marker;
        });
    }

    // Auto-refresh marker positions every 5 seconds
    setInterval(async function(){
        try {
            const resp = await fetch("{{ url('/api/driver-locations') }}", {headers: {Accept: 'application/json'}});
            if (resp.ok) {
                const locations = await resp.json();
                updateMarkers(locations);
            }
        } catch (e) {
            console.error("Error updating map markers:", e);
        }
    }, 5000);

</script>
@endsection
