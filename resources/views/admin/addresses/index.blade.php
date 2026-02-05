@extends('layouts.admin')

@section('title', __('messages.addresses_management'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />
<link href="{{ asset('css/admin/addresses.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="table-card">

    <!-- Search & Filter Section -->
    <div class="search-filter-card" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
        <form method="GET" action="{{ route('addresses.index') }}" id="searchFilterForm">

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                <!-- Search -->
                <div>
                    <label>{{ __('messages.search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('messages.search_by_street_district') }}"
                        style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                </div>

                <!-- City Filter -->
                <div>
                    <label>{{ __('messages.city') }}</label>
                    <select name="city" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">{{ __('messages.all_cities') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->getTranslation('name', 'en') }}" {{ request('city') == $city->getTranslation('name', 'en') ? 'selected' : '' }}>
                                {{ $city->getTranslation('name', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Direction -->
                <div>
                    <label>{{ __('messages.sort_direction') }}</label>
                    <select name="sort_dir" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>{{ __('messages.newest_first') }}</option>
                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>{{ __('messages.oldest_first') }}</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label>{{ __('messages.items_per_page') }}</label>
                    <select name="per_page" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="6" {{ request('per_page', 6) == 6 ? 'selected' : '' }}>{{ __('messages.cards_per_page', ['count' => 6]) }}</option>
                        <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>{{ __('messages.cards_per_page', ['count' => 12]) }}</option>
                        <option value="18" {{ request('per_page') == 18 ? 'selected' : '' }}>{{ __('messages.cards_per_page', ['count' => 18]) }}</option>
                        <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>{{ __('messages.cards_per_page', ['count' => 24]) }}</option>
                    </select>
                </div>

            </div>

            <div style="margin-top:15px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                </button>
                <a href="{{ route('addresses.index') }}"
                    style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                    <i class="fas fa-redo"></i> {{ __('messages.reset') }}
                </a>
            </div>

        </form>
    </div>

    @if (session('success'))
        <div class="alert-auto-hide">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="table-header" style="margin-bottom:20px;">
        <h3>{{ __('messages.addresses_list') }} ({{ __('messages.total_addresses', ['count' => $addresses->total()]) }}) - {{ __('messages.page_of', ['current' => $addresses->currentPage(), 'last' => $addresses->lastPage()]) }}</h3>
    </div>

    <!-- Addresses Grid - Updated for 6 cards per page -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:20px;">
        @forelse ($addresses as $address)
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;transition:all 0.3s;"
                 onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,0.12)';"
                 onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">

                <!-- Header -->
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid #f0f0f0;">
                    <div>
                        <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">{{ __('messages.address_id') }}</div>
                        <div style="font-size:18px;font-weight:700;color:#1f2937;">#{{ $address->id }}</div>
                    </div>
                </div>

                <!-- User Info -->
                <div style="margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#10b981;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:600;">
                            {{ substr($address->user->name ?? 'N', 0, 1) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px;font-weight:600;color:#1f2937;">{{ $address->user->name ?? 'N/A' }}</div>
                            <div style="font-size:12px;color:#6b7280;">{{ $address->user->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Location Details -->
                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
                    <!-- City -->
                    <div style="display:flex;align-items:center;gap:8px;padding:10px;background:#f8fafc;border-radius:8px;">
                        <i class="fas fa-city" style="color:#3b82f6;font-size:14px;"></i>
                        <div style="flex:1;">
                            <div style="font-size:11px;color:#6b7280;">{{ __('messages.city') }}</div>
                            <div style="font-size:13px;font-weight:600;color:#1f2937;">
                                {{ $address->city?->getTranslation('name', 'en') ?? __('messages.n_a') }}
                                <span style="color:#9ca3af;font-weight:400;">/ {{ $address->city?->getTranslation('name', 'ar') ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- District & Street -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div style="padding:10px;background:#f0fdf4;border-radius:8px;">
                            <div style="font-size:11px;color:#065f46;margin-bottom:4px;">
                                <i class="fas fa-location-arrow"></i> {{ __('messages.district') }}
                            </div>
                            <div style="font-size:13px;font-weight:600;color:#065f46;">{{ $address->district }}</div>
                        </div>
                        <div style="padding:10px;background:#fef3c7;border-radius:8px;">
                            <div style="font-size:11px;color:#92400e;margin-bottom:4px;">
                                <i class="fas fa-road"></i> {{ __('messages.house') }}
                            </div>
                            <div style="font-size:13px;font-weight:600;color:#92400e;">{{ $address->house_number }}</div>
                        </div>
                    </div>

                    <!-- Street -->
                    <div style="padding:10px;background:#eff6ff;border-radius:8px;">
                        <div style="font-size:11px;color:#1e40af;margin-bottom:4px;">
                            <i class="fas fa-map"></i> {{ __('messages.street') }}
                        </div>
                        <div style="font-size:13px;font-weight:600;color:#1e40af;">{{ $address->street }}</div>
                    </div>
                </div>

                <!-- Notes (if exists) -->
                @if($address->notes)
                    <div style="padding:10px;background:#fef2f2;border-radius:8px;margin-bottom:16px;">
                        <div style="font-size:11px;color:#991b1b;margin-bottom:4px;">
                            <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}
                        </div>
                        <div style="font-size:12px;color:#7f1d1d;line-height:1.4;">{{ $address->notes }}</div>
                    </div>
                @endif

                <!-- Coordinates (if exists) -->
                @if($address->Langitude && $address->Latitude)
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                        <div style="padding:8px;background:#f8fafc;border-radius:6px;text-align:center;">
                            <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">{{ __('messages.longitude') }}</div>
                            <div style="font-size:12px;font-weight:600;color:#1f2937;">{{ $address->Langitude }}</div>
                        </div>
                        <div style="padding:8px;background:#f8fafc;border-radius:6px;text-align:center;">
                            <div style="font-size:10px;color:#6b7280;margin-bottom:2px;">{{ __('messages.latitude') }}</div>
                            <div style="font-size:12px;font-weight:600;color:#1f2937;">{{ $address->Latitude }}</div>
                        </div>
                    </div>
                @endif

                <!-- Footer -->
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f0f0f0;">
                    <div style="font-size:11px;color:#9ca3af;">
                        <i class="fas fa-clock"></i> {{ $address->created_at->format('M d, Y') }}
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button onclick="showOnMap({{ $address->Latitude ?? 0 }}, {{ $address->Langitude ?? 0 }}, '{{ addslashes($address->user->name ?? 'User') }}')"
                                style="padding:6px 12px;background:#dbeafe;color:#1e40af;border:none;border-radius:6px;cursor:pointer;font-size:11px;font-weight:500;transition:all 0.2s;"
                                onmouseover="this.style.background='#bfdbfe'"
                                onmouseout="this.style.background='#dbeafe'">
                            <i class="fas fa-map-marked-alt"></i> {{ __('messages.map') }}
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
                <i class="fas fa-map-marker-alt" style="font-size:64px;color:#cbd5e0;margin-bottom:20px;"></i>
                <h3 style="color:#4a5568;font-size:20px;margin-bottom:10px;">{{ __('messages.no_addresses_found') }}</h3>
                <p style="color:#9ca3af;font-size:14px;">{{ __('messages.no_addresses_match') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($addresses->hasPages())
        <div class="pagination" style="margin-top:30px;">
            {{ $addresses->appends(request()->except('page'))->links() }}
        </div>
    @endif

</div>

<!-- Map Modal with Real Map -->
<div id="mapModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:12px;padding:20px;max-width:800px;width:90%;max-height:90vh;overflow:auto;position:relative;">
        <button onclick="closeMapModal()" style="position:absolute;top:15px;right:15px;background:#fee2e2;color:#991b1b;border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-weight:bold;">
            ×
        </button>
        <h3 id="mapTitle" style="margin-bottom:15px;color:#1f2937;">{{ __('messages.address_location') }}</h3>
        <div id="map" style="width:100%;height:400px;border-radius:8px;"></div>
        <div id="mapInfo" style="margin-top:15px;padding:15px;background:#f8fafc;border-radius:6px;font-size:14px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <span style="color:#6b7280;">{{ __('messages.latitude') }}:</span>
                    <span id="infoLat" style="font-weight:600;color:#1f2937;"></span>
                </div>
                <div>
                    <span style="color:#6b7280;">{{ __('messages.longitude') }}:</span>
                    <span id="infoLng" style="font-weight:600;color:#1f2937;"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script src="{{ asset('js/admin/addresses.js') }}"></script>
@endpush
