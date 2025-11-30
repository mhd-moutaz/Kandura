@extends('layouts.admin')

@section('title', 'Addresses Management')

@section('content')

    <!-- Search & Filter -->
    <div class="table-card">

        <div class="search-filter-card" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
            <form method="GET" action="{{ route('addresses.index') }}" id="searchFilterForm">

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                    <div>
                        <label>Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by street, district, or house number..."
                               style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                    </div>

                    <div>
                        <label>City</label>
                        <select name="city"
                                style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="">All Cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->name }}"
                                        {{ request('city') == $city->name ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Sort Dir</label>
                        <select name="sort_dir"
                                style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>ASC</option>
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>DESC</option>
                        </select>
                    </div>

                </div>

                <div style="margin-top:15px;display:flex;gap:10px;">
                    <button type="submit" style="background:#3b82f6;color:white;padding:8px 16px;border:none;border-radius:6px;">
                        Apply Filters
                    </button>
                    <a href="{{ route('addresses.index') }}"
                       style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        @if (session('success'))
            <div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:6px;margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Addresses Table -->
        <table>
            <thead>
                <tr>
                    <th>Address ID</th>
                    <th>User</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>District</th>
                    <th>Street</th>
                    <th>House Number</th>
                    <th>Notes</th>
                    <th>Longitude</th>
                    <th>Latitude</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($addresses as $address)
                    <tr>
                        <td>#Addr-{{ $address->id }}</td>
                        <td>{{ $address->user->name ?? 'N/A' }}</td>
                        <td>{{ $address->user->phone ?? 'N/A' }}</td>
                        <td>{{ $address->city->name ?? 'N/A' }}</td>
                        <td>{{ $address->district }}</td>
                        <td>{{ $address->street }}</td>
                        <td>{{ $address->house_number }}</td>
                        <td>{{ $address->notes ?? 'N/A' }}</td>
                        <td>{{ $address->longitude ?? 'N/A' }}</td>
                        <td>{{ $address->latitude ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:20px;color:#6b7280;">
                            No addresses found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($addresses->hasPages())
            <div class="pagination">
                {{ $addresses->links() }}
            </div>
        @endif

    </div>


    <!-- MAP SECTION -->
    <h3 style="margin-top:30px;margin-bottom:10px;">Addresses Map</h3>
    <div id="map" style="width:100%;height:450px;border-radius:10px;"></div>


    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var map = L.map('map').setView([30.7136, 31.6753], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18
        }).addTo(map);

        var markers = [];
        var addresses = @json($addresses);

        addresses.forEach(function (address) {
            if (address.latitude && address.longitude) {

                var marker = L.marker([address.latitude, address.longitude])
                    .addTo(map)
                    .bindPopup(
                        `<b>${address.user?.name ?? 'N/A'}</b><br>
                         ${address.city?.name ?? ''} - ${address.district}<br>
                         ${address.street} - ${address.house_number}`
                    );

                markers.push(marker);
            }
        });

        if (markers.length > 0) {
            var group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds());
        }
    </script>

@endsection
