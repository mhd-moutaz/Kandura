@extends('layouts.admin')

@section('title', 'Design Details')

@push('styles')
    <link href="{{ asset('css/admin/designs.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div style="max-width:1400px;margin:0 auto;">

        <!-- Back Button & Header -->
        <div
            style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <a href="{{ route('designs.index') }}"
                    style="display:flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;padding:8px 14px;background:white;border-radius:6px;border:1px solid #e2e8f0;transition:all 0.2s;font-size:14px;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Designs</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                style="background:#d1fae5;color:#065f46;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #10b981;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background:#fee2e2;color:#991b1b;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #ef4444;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:20px;">

            <!-- Left Column: Images Gallery -->
            <div>
                <div
                    style="background:white;border-radius:10px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                    <h3
                        style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-images" style="color:#3b82f6;"></i>
                        Design Images
                    </h3>

                    @if ($design->designImages->count() > 0)
                        <!-- Main Image Display -->
                        <div
                            style="position:relative;margin-bottom:16px;border-radius:10px;overflow:hidden;background:#f8fafc;">
                            <div id="mainImageContainer"
                                style="height:400px;display:flex;align-items:center;justify-content:center;">
                                @foreach ($design->designImages as $index => $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ $design->name['ar'] ?? 'Design' }}" class="main-gallery-image"
                                        data-index="{{ $index }}"
                                        style="width:100%;height:100%;object-fit:contain;display:{{ $index == 0 ? 'block' : 'none' }};">
                                @endforeach
                            </div>

                            @if ($design->designImages->count() > 1)
                                <!-- Navigation Arrows -->
                                <button onclick="changeMainImage(-1)"
                                    style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.7);color:white;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;font-size:16px;">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button onclick="changeMainImage(1)"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.7);color:white;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;font-size:16px;">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Thumbnail Gallery -->
                        @if ($design->designImages->count() > 1)
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:10px;">
                                @foreach ($design->designImages as $index => $image)
                                    <div onclick="showMainImage({{ $index }})" class="thumbnail-image"
                                        data-index="{{ $index }}"
                                        style="height:90px;border-radius:8px;overflow:hidden;cursor:pointer;border:2px solid {{ $index == 0 ? '#3b82f6' : '#e2e8f0' }};transition:all 0.3s;">
                                        <img src="{{ asset('storage/' . $image->image_path) }}"
                                            alt="Thumbnail {{ $index + 1 }}"
                                            style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div
                            style="height:350px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f8fafc;border-radius:10px;">
                            <i class="fas fa-image" style="font-size:60px;color:#cbd5e0;margin-bottom:12px;"></i>
                            <p style="color:#9ca3af;font-size:14px;">No images available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Design Information -->
            <div style="display:flex;flex-direction:column;gap:24px;">

                <!-- Basic Information -->
                <div
                    style="background:white;border-radius:10px;padding:36px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <h3
                            style="font-size:18px;font-weight:600;color:#1f2937;display:flex;align-items:center;gap:8px;margin:0;">
                            <i class="fas fa-info-circle" style="color:#3b82f6;"></i>
                            Basic Information
                        </h3>
                        <span
                            style="background:#edf2f7;color:#4a5568;padding:4px 10px;border-radius:6px;font-size:13px;font-weight:600;">
                            ID: #{{ $design->id }}
                        </span>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:12px;">

                        <!-- Name -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                Name (Arabic)
                            </label>
                            <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:14px;">
                                {{ $design->name['ar'] ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                Name (English)
                            </label>
                            <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:14px;">
                                {{ $design->name['en'] ?? 'N/A' }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                Description (Arabic)
                            </label>
                            <div
                                style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;line-height:1.5;min-height:50px;">
                                {{ $design->description['ar'] ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                Description (English)
                            </label>
                            <div
                                style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;line-height:1.5;min-height:50px;">
                                {{ $design->description['en'] ?? 'N/A' }}
                            </div>
                        </div>

                        <!-- Price -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                Price
                            </label>
                            <div
                                style="padding:14px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:8px;color:white;font-size:24px;font-weight:700;text-align:center;">
                                ${{ number_format($design->price, 2) }}
                            </div>
                        </div>

                        <!-- State -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                Status
                            </label>
                            <div
                                style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:{{ $design->state ? '#f0fdf4' : '#fef2f2' }};border-radius:8px;border:1px solid {{ $design->state ? '#bbf7d0' : '#fecaca' }};">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span
                                        style="width:12px;height:12px;border-radius:50%;background:{{ $design->state ? '#22c55e' : '#ef4444' }};"></span>
                                    <span
                                        style="font-size:14px;font-weight:600;color:{{ $design->state ? '#16a34a' : '#dc2626' }};">
                                        {{ $design->state ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <form action="{{ route('designs.toggleState', $design->id) }}" method="POST"
                                    style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        onclick="return confirm('{{ $design->state ? 'Are you sure you want to deactivate this design?' : 'Are you sure you want to activate this design?' }}')"
                                        style="padding:8px 16px;background:{{ $design->state ? '#dc2626' : '#16a34a' }};color:white;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:500;transition:all 0.2s;display:flex;align-items:center;gap:6px;"
                                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                        <i class="fas {{ $design->state ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        {{ $design->state ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Creator & Dates -->
        <div
            style="background:white;border-radius:10px;padding:20px;margin: 10px 0px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
            <h3
                style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i class="fas fa-user-circle" style="color:#6366f1;"></i>
                Creator & Dates
            </h3>

            <!-- Created By -->
            <div style="margin-bottom:12px;">
                <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                    Created By
                </label>
                <div
                    style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;display:flex;align-items:center;gap:6px;">
                    <div
                        style="width:28px;height:28px;border-radius:50%;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:600;">
                        {{ substr($design->user->name ?? 'N', 0, 1) }}
                    </div>
                    <span>{{ $design->user->name ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Dates Row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                        Created Date
                    </label>
                    <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;">
                        {{ $design->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                        Last Updated
                    </label>
                    <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;">
                        {{ $design->updated_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Measurements -->
        @if ($design->measurements && $design->measurements->count() > 0)
            <div
                style="background:white;border-radius:10px;padding:20px;margin: 10px 0px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3
                    style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-ruler-combined" style="color:#10b981;"></i>
                    Available Sizes & Measurements
                </h3>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;">
                    @foreach ($design->measurements as $measurement)
                        <div
                            style="padding:12px;background:#f0fdf4;border:2px solid #86efac;border-radius:8px;text-align:center;">
                            <div style="font-size:20px;font-weight:700;color:#065f46;margin-bottom:2px;">
                                {{ $measurement->size }}
                            </div>
                            <div style="font-size:10px;color:#059669;font-weight:500;letter-spacing:0.5px;">
                                SIZE
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Design Options -->
        @if ($design->designOptions && $design->designOptions->count() > 0)
            <div
                style="background:white;border-radius:10px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3
                    style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-palette" style="color:#f59e0b;"></i>
                    Design Options
                </h3>

                <div style="display:flex;flex-direction:column;gap:12px;">
                    @php
                        $typeColors = [
                            'background' => '#fefce8',
                            'border' => '#f59e0b',
                            'text' => '#92400e',
                            'itemBorder' => '#fcd34d',
                        ];
                        $alternateColors = [
                            'background' => '#eff6ff',
                            'border' => '#3b82f6',
                            'text' => '#1e40af',
                            'itemBorder' => '#93c5fd',
                        ];
                        $colorIndex = 0;
                    @endphp

                    @foreach ($design->designOptions->groupBy('type') as $type => $options)
                        @php
                            $colors = $colorIndex % 2 == 0 ? $typeColors : $alternateColors;
                            $colorIndex++;
                        @endphp
                        <div
                            style="padding:12px;background:{{ $colors['background'] }};border-radius:8px;border-left:3px solid {{ $colors['border'] }};">
                            <div
                                style="font-size:12px;color:{{ $colors['text'] }};font-weight:600;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                                {{ $type }}
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach ($options as $option)
                                    <span
                                        style="padding:6px 12px;background:white;color:{{ $colors['text'] }};border-radius:6px;font-size:12px;font-weight:500;border:1px solid {{ $colors['itemBorder'] }};">
                                        {{ $option->name['en'] ?? ($option->name['ar'] ?? 'N/A') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    </div>

    </div>

@endsection

@push('scripts')
    <script>
        const totalImages = {{ $design->designImages->count() }};
    </script>
    <script src="{{ asset('js/admin/designs-show.js') }}"></script>
@endpush
