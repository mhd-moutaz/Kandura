@extends('layouts.admin')

@section('title', 'Designs Management')

@push('styles')
<link href="{{ asset('css/admin/designs.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- Search & Filter -->
    <div class="table-card">
        <div class="search-filter-card" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
            <form method="GET" action="{{ route('designs.index') }}" id="searchFilterForm">

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                    <div>
                        <label>Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                            style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                    </div>

                    <div>
                        <label>Size</label>
                        <select name="size" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="">All Sizes</option>
                            <option value="XS" {{ request('size') == 'XS' ? 'selected' : '' }}>XS</option>
                            <option value="S" {{ request('size') == 'S' ? 'selected' : '' }}>S</option>
                            <option value="M" {{ request('size') == 'M' ? 'selected' : '' }}>M</option>
                            <option value="L" {{ request('size') == 'L' ? 'selected' : '' }}>L</option>
                            <option value="XL" {{ request('size') == 'XL' ? 'selected' : '' }}>XL</option>
                            <option value="XXL" {{ request('size') == 'XXL' ? 'selected' : '' }}>XXL</option>
                        </select>
                    </div>

                    <div>
                        <label>Min Price</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0"
                            style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                    </div>

                    <div>
                        <label>Max Price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="1000"
                            style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                    </div>

                    <div>
                        <label>Design Option Type</label>
                        <select name="design_option_type" id="designOptionType"
                            style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="">All Types</option>
                            @foreach ($designOptions->pluck('type')->unique() as $type)
                                <option value="{{ $type }}"
                                    {{ request('design_option_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="designOptionValuesContainer"
                        style="display: {{ request('design_option_type') ? 'block' : 'none' }};">
                        <label>Design Option Value</label>
                        <select name="design_option" id="designOptionValue"
                            style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="">All Values</option>
                            @if (request('design_option_type'))
                                @foreach ($designOptions->where('type', request('design_option_type')) as $option)
                                    <option value="{{ $option->id }}"
                                        {{ request('design_option') == $option->id ? 'selected' : '' }}>
                                        {{ $option->name['en'] ?? ($option->name['ar'] ?? 'N/A') }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label>Sort By</label>
                        <select name="sort_by" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created
                                Date</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                            <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Updated
                                Date</option>
                        </select>
                    </div>

                    <div>
                        <label>Sort Direction</label>
                        <select name="sort_dir" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>ASC</option>
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>DESC</option>
                        </select>
                    </div>

                </div>

                <div style="margin-top:15px;display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('designs.index') }}"
                        style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>

            </form>
        </div>


        @if (session('success'))
            <div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:6px;margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-header">
            <h3>Designs List ({{ $designs->total() }} designs)</h3>
        </div>

        <!-- Designs Cards Grid -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:20px;">
            @forelse ($designs as $design)
                <div class="design-card" data-href="{{ route('designs.show', $design->id) }}" role="button" tabindex="0"
                    onclick="window.location='{{ route('designs.show', $design->id) }}';"
                    style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:all 0.3s;border:1px solid #e2e8f0;cursor:pointer;">

                    <!-- Design Image -->
                    <div style="height:200px;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;"
                        class="design-image-slider" data-design-id="{{ $design->id }}">
                        @if ($design->designImages->count() > 0)
                            @foreach ($design->designImages as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="{{ $design->name['ar'] ?? 'Design' }}"
                                    class="slider-image slider-image-{{ $design->id }}"
                                    style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;opacity:{{ $index == 0 ? '1' : '0' }};transition:opacity 0.5s ease;">
                            @endforeach

                            <!-- Navigation Arrows -->
                            @if ($design->designImages->count() > 1)
                                <button onclick="event.stopPropagation(); changeSlide({{ $design->id }}, -1)"
                                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.6);color:white;border:none;width:35px;height:35px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;transition:all 0.3s;"
                                    onmouseover="this.style.background='rgba(0,0,0,0.8)'"
                                    onmouseout="this.style.background='rgba(0,0,0,0.6)'">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button onclick="event.stopPropagation(); changeSlide({{ $design->id }}, 1)"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.6);color:white;border:none;width:35px;height:35px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;transition:all 0.3s;"
                                    onmouseover="this.style.background='rgba(0,0,0,0.8)'"
                                    onmouseout="this.style.background='rgba(0,0,0,0.6)'">
                                    <i class="fas fa-chevron-right"></i>
                                </button>

                                <!-- Dots Indicator -->
                                <div
                                    style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:10;">
                                    @foreach ($design->designImages as $index => $image)
                                        <span class="slider-dot slider-dot-{{ $design->id }}"
                                            onclick="event.stopPropagation(); goToSlide({{ $design->id }}, {{ $index }})"
                                            style="width:8px;height:8px;border-radius:50%;background:{{ $index == 0 ? 'white' : 'rgba(255,255,255,0.5)' }};cursor:pointer;transition:all 0.3s;"></span>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div style="text-align:center;color:#9ca3af;">
                                <i class="fas fa-image" style="font-size:48px;margin-bottom:10px;"></i>
                                <p style="font-size:14px;">No Image</p>
                            </div>
                        @endif

                        <!-- Images Count Badge -->
                        @if ($design->designImages->count() > 0)
                            <span
                                style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.7);color:white;padding:4px 10px;border-radius:20px;font-size:12px;z-index:10;">
                                <i class="fas fa-images"></i> {{ $design->designImages->count() }}
                            </span>
                        @endif
                    </div>

                    <!-- Design Info -->
                    <div style="padding:20px;">

                        <!-- ID Badge -->
                        <div style="margin-bottom:10px;">
                            <span
                                style="background:#edf2f7;color:#4a5568;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                                #{{ $design->id }}
                            </span>
                        </div>

                        <!-- Name -->
                        <h4
                            style="font-size:18px;font-weight:600;color:#2d3748;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $design->name['ar'] ?? ($design->name['en'] ?? 'N/A') }}
                        </h4>

                        <!-- Description -->
                        @if (isset($design->description['ar']) || isset($design->description['en']))
                            <p
                                style="font-size:13px;color:#718096;margin-bottom:15px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $design->description['ar'] ?? ($design->description['en'] ?? '') }}
                            </p>
                        @endif

                        <!-- Price -->
                        <div
                            style="background:#f0f4ff;padding:10px;border-radius:8px;margin-bottom:15px;text-align:center;">
                            <div style="font-size:11px;color:#5a67d8;margin-bottom:2px;">السعر</div>
                            <div style="font-size:18px;font-weight:700;color:#5a67d8;">
                                ${{ number_format($design->price, 2) }}
                            </div>
                        </div>

                        <!-- Creator & Date -->
                        <div
                            style="display:flex;justify-content:space-between;align-items:center;padding-bottom:15px;border-bottom:1px solid #e2e8f0;margin-bottom:15px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div
                                    style="width:32px;height:32px;border-radius:50%;background:#5a67d8;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:600;">
                                    {{ substr($design->user->name ?? 'N', 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:500;color:#2d3748;">
                                        {{ $design->user->name ?? 'N/A' }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">{{ $design->created_at->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Measurements Info -->
                        @if ($design->measurements->count() > 0)
                            <div style="margin-bottom:15px;">
                                <div style="font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Available
                                    Sizes:</div>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @foreach ($design->measurements->take(5) as $measurement)
                                        <span
                                            style="background:#f0fdf4;color:#065f46;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:500;">
                                            {{ $measurement->size }}
                                        </span>
                                    @endforeach
                                    @if ($design->measurements->count() > 5)
                                        <span
                                            style="background:#f0fdf4;color:#065f46;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:500;">
                                            +{{ $design->measurements->count() - 5 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        {{-- <div style="display:flex;gap:8px;">
                            <a href="{{ route('designs.edit', $design->id) }}" onclick="event.stopPropagation();"
                               style="flex:1;text-align:center;padding:10px;background:#dbeafe;color:#1e40af;border-radius:8px;text-decoration:none;font-size:13px;font-weight:500;transition:all 0.2s;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('designs.destroy', $design->id) }}" method="POST" style="flex:1;"
                                  onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this design?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="width:100%;padding:10px;background:#fee2e2;color:#991b1b;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.2s;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div> --}}

                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
                    <i class="fas fa-inbox" style="font-size:64px;color:#cbd5e0;margin-bottom:20px;"></i>
                    <h3 style="color:#4a5568;font-size:20px;margin-bottom:10px;">No Designs Found</h3>
                    <p style="color:#9ca3af;font-size:14px;">Try adjusting your filters or create a new design.</p>
                </div>
            @endforelse
        </div>

        @if ($designs->hasPages())
            <div class="pagination">
                {{ $designs->links() }}
            </div>
        @endif

    </div>

@endsection

@push('scripts')
<script>
    // Pass design options data to JavaScript
    const designOptionsData = @json($designOptions);
</script>
<script src="{{ asset('js/admin/designs.js') }}"></script>
<script>
    // Replace the designOptions reference in designs.js
    if (typeof designOptions === 'undefined') {
        var designOptions = designOptionsData;
    }
</script>
@endpush
