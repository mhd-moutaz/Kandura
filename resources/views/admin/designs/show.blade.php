@extends('layouts.admin')

@section('title', __('messages.design_details'))

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
                    <span>{{ __('messages.back_to_designs') }}</span>
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
                        {{ __('messages.design_images') }}
                    </h3>

                    @if ($design->designImages->count() > 0)
                        <!-- Main Image Display -->
                        <div
                            style="position:relative;margin-bottom:16px;border-radius:10px;overflow:hidden;background:#f8fafc;">
                            <div id="mainImageContainer"
                                style="height:400px;display:flex;align-items:center;justify-content:center;">
                                @foreach ($design->designImages as $index => $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ $design->getTranslation('name', app()->getLocale()) }}" class="main-gallery-image"
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
                            <p style="color:#9ca3af;font-size:14px;">{{ __('messages.no_images_available') }}</p>
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
                            {{ __('messages.basic_information') }}
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
                                {{ __('messages.name_arabic') }}
                            </label>
                            <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:14px;">
                                {{ $design->getTranslation('name', 'ar') ?: __('messages.n_a') }}
                            </div>
                        </div>

                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                {{ __('messages.name_english') }}
                            </label>
                            <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:14px;">
                                {{ $design->getTranslation('name', 'en') ?: __('messages.n_a') }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                {{ __('messages.description') }} ({{ __('messages.name_ar') }})
                            </label>
                            <div
                                style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;line-height:1.5;min-height:50px;">
                                {{ $design->getTranslation('description', 'ar') ?: __('messages.n_a') }}
                            </div>
                        </div>

                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                {{ __('messages.description') }} ({{ __('messages.name_en') }})
                            </label>
                            <div
                                style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;line-height:1.5;min-height:50px;">
                                {{ $design->getTranslation('description', 'en') ?: __('messages.n_a') }}
                            </div>
                        </div>

                        <!-- Price -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                {{ __('messages.price') }}
                            </label>
                            <div
                                style="padding:14px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:8px;color:white;font-size:24px;font-weight:700;text-align:center;">
                                ${{ number_format($design->price, 2) }}
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                {{ __('messages.available_quantity') }}
                            </label>
                            <div
                                style="padding:14px;background:{{ $design->quantity > 0 ? 'linear-gradient(135deg,#6ee7b7 0%,#10b981 100%)' : 'linear-gradient(135deg,#fca5a5 0%,#ef4444 100%)' }};border-radius:8px;color:white;font-size:24px;font-weight:700;text-align:center;">
                                {{ $design->quantity }} {{ __('messages.units') ?? 'units' }}
                                @if($design->quantity == 0)
                                    <div style="font-size:11px;margin-top:4px;opacity:0.9;">{{ __('messages.out_of_stock') }}</div>
                                @elseif($design->quantity < 10)
                                    <div style="font-size:11px;margin-top:4px;opacity:0.9;">{{ __('messages.low_stock') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- State -->
                        <div>
                            <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                                {{ __('messages.status') }}
                            </label>
                            <div
                                style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:{{ $design->state ? '#f0fdf4' : '#fef2f2' }};border-radius:8px;border:1px solid {{ $design->state ? '#bbf7d0' : '#fecaca' }};">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span
                                        style="width:12px;height:12px;border-radius:50%;background:{{ $design->state ? '#22c55e' : '#ef4444' }};"></span>
                                    <span
                                        style="font-size:14px;font-weight:600;color:{{ $design->state ? '#16a34a' : '#dc2626' }};">
                                        {{ $design->state ? __('messages.active') : __('messages.inactive') }}
                                    </span>
                                </div>
                                <form action="{{ route('designs.toggleState', $design->id) }}" method="POST"
                                    style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        onclick="return confirm('{{ $design->state ? __('messages.deactivate_confirm') : __('messages.activate_confirm') }}')"
                                        style="padding:8px 16px;background:{{ $design->state ? '#dc2626' : '#16a34a' }};color:white;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:500;transition:all 0.2s;display:flex;align-items:center;gap:6px;"
                                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                        <i class="fas {{ $design->state ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        {{ $design->state ? __('messages.deactivate') : __('messages.activate') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Quantity Management -->
        <div
            style="background:white;border-radius:10px;padding:20px;margin:10px 0px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
            <h3
                style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i class="fas fa-box" style="color:#8b5cf6;"></i>
                {{ __('messages.quantity_management') ?? 'Quantity Management' }}
            </h3>

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">

                <!-- Increment Quantity -->
                <form action="{{ route('designs.updateQuantity', $design->id) }}" method="POST"
                    style="background:#f0fdf4;border:2px solid #10b981;border-radius:8px;padding:16px;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="increment">

                    <div style="margin-bottom:12px;">
                        <label style="font-size:12px;color:#065f46;font-weight:600;display:block;margin-bottom:6px;">
                            <i class="fas fa-plus-circle"></i> {{ __('messages.add_stock') ?? 'Add Stock' }}
                        </label>
                        <input type="number" name="quantity" min="1" max="999999" required
                            style="width:100%;padding:10px;border:1px solid #86efac;border-radius:6px;font-size:14px;font-weight:500;">
                    </div>

                    <button type="submit"
                        style="width:100%;padding:10px;background:#10b981;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:6px;"
                        onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                        <i class="fas fa-arrow-up"></i> {{ __('messages.increase') ?? 'Increase' }}
                    </button>
                </form>

                <!-- Decrement Quantity -->
                <form action="{{ route('designs.updateQuantity', $design->id) }}" method="POST"
                    style="background:#fef2f2;border:2px solid #ef4444;border-radius:8px;padding:16px;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="decrement">

                    <div style="margin-bottom:12px;">
                        <label style="font-size:12px;color:#991b1b;font-weight:600;display:block;margin-bottom:6px;">
                            <i class="fas fa-minus-circle"></i> {{ __('messages.remove_stock') ?? 'Remove Stock' }}
                        </label>
                        <input type="number" name="quantity" min="1" max="{{ $design->quantity }}" required
                            style="width:100%;padding:10px;border:1px solid #fca5a5;border-radius:6px;font-size:14px;font-weight:500;">
                    </div>

                    <button type="submit"
                        style="width:100%;padding:10px;background:#ef4444;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:6px;"
                        onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'"
                        onclick="return confirm('{{ __('messages.confirm_decrease_stock') ?? 'Are you sure you want to decrease stock?' }}')">
                        <i class="fas fa-arrow-down"></i> {{ __('messages.decrease') ?? 'Decrease' }}
                    </button>
                </form>

            </div>

            <div style="margin-top:16px;padding:12px;background:#eff6ff;border-radius:6px;border-left:3px solid #3b82f6;">
                <div style="display:flex;align-items:start;gap:8px;">
                    <i class="fas fa-info-circle" style="color:#3b82f6;margin-top:2px;"></i>
                    <div style="font-size:12px;color:#1e40af;line-height:1.5;">
                        <strong>{{ __('messages.note') ?? 'Note' }}:</strong>
                        {{ __('messages.quantity_management_note') ?? 'Quantity is automatically reduced when orders are confirmed and restored when orders are cancelled within 1 hour.' }}
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
                {{ __('messages.creator_dates') }}
            </h3>

            <!-- Created By -->
            <div style="margin-bottom:12px;">
                <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                    {{ __('messages.created_by') }}
                </label>
                <div
                    style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;display:flex;align-items:center;gap:6px;">
                    <div
                        style="width:28px;height:28px;border-radius:50%;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:600;">
                        {{ substr($design->user->name ?? 'N', 0, 1) }}
                    </div>
                    <span>{{ $design->user->name ?? __('messages.n_a') }}</span>
                </div>
            </div>

            <!-- Dates Row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                        {{ __('messages.created_date') }}
                    </label>
                    <div style="padding:10px;background:#f8fafc;border-radius:6px;color:#1f2937;font-size:13px;">
                        {{ $design->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                <div>
                    <label style="font-size:12px;color:#6b7280;font-weight:500;display:block;margin-bottom:4px;">
                        {{ __('messages.last_updated') }}
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
                    {{ __('messages.available_sizes_measurements') }}
                </h3>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;">
                    @foreach ($design->measurements as $measurement)
                        <div
                            style="padding:12px;background:#f0fdf4;border:2px solid #86efac;border-radius:8px;text-align:center;">
                            <div style="font-size:20px;font-weight:700;color:#065f46;margin-bottom:2px;">
                                {{ $measurement->size }}
                            </div>
                            <div style="font-size:10px;color:#059669;font-weight:500;letter-spacing:0.5px;">
                                {{ __('messages.size') }}
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
                    {{ __('messages.design_options') }}
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
                                        style="padding:6px 12px;background:white;color:{{ $colors['text'] }};border-radius:6px;font-size:12px;font-weight:500;border:1px solid {{ $colors['itemBorder'] }}; display: inline-flex; align-items: center; gap: 6px;">
                                        @if($option->type === 'color' && $option->hex_color)
                                            <span style="width: 16px; height: 16px; border-radius: 3px; border: 1px solid rgba(0,0,0,0.1); background: {{ $option->hex_color }}; display: inline-block;"></span>
                                        @endif
                                        {{ $option->getTranslation('name', app()->getLocale()) ?? __('messages.n_a') }}
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
