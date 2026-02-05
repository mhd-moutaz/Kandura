@extends('layouts.admin')

@section('title', __('messages.order_details') . ' #' . $order->id)

@push('styles')
<link href="{{ asset('css/admin/orders.css') }}" rel="stylesheet">
@endpush

@section('content')

<div style="max-width:1400px;margin:0 auto;">

    <!-- Back Button -->
    <div style="margin-bottom:20px;">
        <a href="{{ route('orders.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;padding:8px 14px;background:white;border-radius:6px;border:1px solid #e2e8f0;transition:all 0.2s;font-size:14px;">
            <i class="fas fa-arrow-left"></i>
            <span>{{ __('messages.back_to_orders') }}</span>
        </a>
    </div>

    @if (session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #10b981;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 380px;gap:20px;">

        <!-- Main Content -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- Order Header -->
            <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:20px;">
                    <div>
                        <h2 style="font-size:24px;font-weight:700;color:#1f2937;margin-bottom:8px;">{{ __('messages.order_details') }} #{{ $order->id }}</h2>
                        <p style="color:#6b7280;font-size:14px;">
                            <i class="fas fa-clock"></i> {{ $order->created_at->format('F d, Y - H:i') }}
                        </p>
                    </div>
                    @php
                        $statusColors = [
                            'confirmed' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'icon' => 'check-circle'],
                            'processing' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'icon' => 'cog'],
                            'completed' => ['bg' => '#d1fae5', 'text' => '#065f46', 'icon' => 'check-double'],
                            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'times-circle'],
                        ];
                        $color = $statusColors[$order->status] ?? ['bg' => '#f3f4f6', 'text' => '#4b5563', 'icon' => 'question'];
                    @endphp
                    <span style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:24px;font-size:14px;font-weight:600;background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                        <i class="fas fa-{{ $color['icon'] }}"></i>
                        {{ __('messages.' . $order->status) }}
                    </span>
                </div>

                <!-- Quick Stats -->
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                    <div style="background:#f8fafc;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.total_items') }}</div>
                        <div style="font-size:24px;font-weight:700;color:#1f2937;">{{ $order->orderItems->count() }}</div>
                    </div>
                    <div style="background:#f0fdf4;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.total_amount') }}</div>
                        <div style="font-size:24px;font-weight:700;color:#10b981;">${{ number_format($order->total, 2) }}</div>
                    </div>
                    <div style="background:#eff6ff;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.payment_method') }}</div>
                        <div style="font-size:16px;font-weight:600;color:#1e40af;text-transform:capitalize;">
                            <i class="fas fa-{{ $order->payment_method == 'cash' ? 'money-bill-wave' : ($order->payment_method == 'card' ? 'credit-card' : 'wallet') }}"></i>
                            {{ __('messages.' . $order->payment_method) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-shopping-bag" style="color:#3b82f6;"></i>
                    {{ __('messages.order_items') }}
                </h3>

                <div style="display:flex;flex-direction:column;gap:16px;">
                    @foreach($order->orderItems as $item)
                        <div style="display:flex;gap:16px;padding:16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
                            <!-- Image -->
                            <div style="width:100px;height:100px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#f0f0f0;">
                                @if($item->design->designImages->count() > 0)
                                    <img src="{{ asset('storage/' . $item->design->designImages->first()->image_path) }}"
                                         alt="{{ $item->design->getTranslation('name', app()->getLocale()) }}"
                                         style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                                        <i class="fas fa-image" style="font-size:32px;"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div style="flex:1;">
                                <h4 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:8px;">
                                    {{ $item->design->getTranslation('name', app()->getLocale()) }}
                                </h4>

                                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                                    <!-- Size -->
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#f0fdf4;color:#065f46;border-radius:6px;font-size:12px;font-weight:500;">
                                        <i class="fas fa-ruler-combined"></i>
                                        {{ __('messages.size') }}: {{ $item->measurement->size }}
                                    </span>

                                    <!-- Quantity -->
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#dbeafe;color:#1e40af;border-radius:6px;font-size:12px;font-weight:500;">
                                        <i class="fas fa-boxes"></i>
                                        {{ __('messages.qty') }}: {{ $item->quantity }}
                                    </span>

                                    <!-- Design Options -->
                                    @foreach($item->designOptions as $option)
                                        <span style="padding:4px 10px;background:#fef3c7;color:#92400e;border-radius:6px;font-size:12px;font-weight:500;">
                                            {{ $option->getTranslation('name', app()->getLocale()) ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </div>

                                <!-- Pricing -->
                                <div style="display:flex;justify-content:space-between;align-items:end;">
                                    <div style="font-size:12px;color:#6b7280;">
                                        {{ __('messages.unit_price') }}: <span style="font-weight:600;color:#1f2937;">${{ number_format($item->unit_price, 2) }}</span>
                                    </div>
                                    <div style="font-size:18px;font-weight:700;color:#10b981;">
                                        ${{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Total Summary -->
                <div style="margin-top:20px;padding-top:20px;border-top:2px solid #e2e8f0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:18px;font-weight:600;color:#1f2937;">{{ __('messages.order_total') }}</span>
                        <span style="font-size:28px;font-weight:700;color:#10b981;">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- Update Status -->
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-edit" style="color:#3b82f6;"></i>
                    {{ __('messages.update_status') }}
                </h3>

                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">
                            {{ __('messages.select_new_status') }}
                        </label>
                        <select name="status" required
                                style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>{{ __('messages.confirmed') }}</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>{{ __('messages.processing') }}</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="fas fa-save"></i> {{ __('messages.update_status') }}
                    </button>
                </form>
            </div>

            <!-- Customer Info -->
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-user" style="color:#3b82f6;"></i>
                    {{ __('messages.customer_information') }}
                </h3>

                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:#f8fafc;border-radius:8px;">
                        <div style="width:48px;height:48px;border-radius:50%;background:#5a67d8;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:600;">
                            {{ substr($order->user->name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:600;color:#1f2937;">{{ $order->user->name ?? 'N/A' }}</div>
                            <div style="font-size:12px;color:#6b7280;">{{ $order->user->email ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div style="padding:12px;background:#f8fafc;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">{{ __('messages.phone') }}</div>
                        <div style="font-size:14px;font-weight:500;color:#1f2937;">{{ $order->user->phone ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-map-marker-alt" style="color:#10b981;"></i>
                    {{ __('messages.delivery_address') }}
                </h3>

                <div style="font-size:14px;color:#4b5563;line-height:1.6;">
                    <p style="margin-bottom:8px;">
                        <strong>{{ $order->address->city->getTranslation('name', app()->getLocale()) }}</strong>
                    </p>
                    <p style="margin-bottom:4px;">{{ $order->address->district }}</p>
                    <p style="margin-bottom:4px;">{{ $order->address->street }}</p>
                    <p style="margin-bottom:4px;">{{ __('messages.house') }}: {{ $order->address->house_number }}</p>
                    @if($order->address->notes)
                        <p style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:6px;font-size:12px;color:#6b7280;">
                            <strong>{{ __('messages.note') }}:</strong> {{ $order->address->notes }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Order Note -->
            @if($order->note)
                <div style="background:#fff7ed;border-radius:12px;padding:20px;border:1px solid #fed7aa;">
                    <h3 style="font-size:16px;font-weight:600;color:#9a3412;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-sticky-note"></i>
                        {{ __('messages.order_note') }}
                    </h3>
                    <p style="font-size:14px;color:#7c2d12;line-height:1.6;">{{ $order->note }}</p>
                </div>
            @endif

            <!-- Customer Review -->
            @if($order->review)
                <div style="background:#faf5ff;border-radius:12px;padding:20px;border:1px solid #e9d5ff;">
                    <h3 style="font-size:16px;font-weight:600;color:#7c3aed;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-star"></i>
                        {{ __('messages.customer_review') }}
                    </h3>

                    <!-- Rating Stars -->
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                        <div style="display:flex;gap:2px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="font-size:18px;color:{{ $i <= $order->review->rating ? '#fbbf24' : '#d1d5db' }};"></i>
                            @endfor
                        </div>
                        <span style="font-size:14px;font-weight:600;color:#1f2937;">{{ $order->review->rating }}/5</span>
                    </div>

                    <!-- Review Comment -->
                    @if($order->review->comment)
                        <div style="background:white;padding:14px;border-radius:8px;border:1px solid #e9d5ff;">
                            <p style="font-size:14px;color:#4b5563;line-height:1.6;margin:0;font-style:italic;">
                                "{{ $order->review->comment }}"
                            </p>
                        </div>
                    @endif

                    <!-- Review Date -->
                    <p style="font-size:12px;color:#6b7280;margin-top:12px;margin-bottom:0;">
                        <i class="fas fa-clock"></i> {{ __('messages.reviewed_on') }} {{ $order->review->created_at->format('F d, Y') }}
                    </p>
                </div>
            @else
                @if($order->status === 'completed')
                    <div style="background:#f8fafc;border-radius:12px;padding:20px;border:1px dashed #cbd5e1;">
                        <div style="text-align:center;color:#6b7280;">
                            <i class="fas fa-comment-slash" style="font-size:24px;margin-bottom:8px;"></i>
                            <p style="font-size:14px;margin:0;">{{ __('messages.no_review_yet') }}</p>
                        </div>
                    </div>
                @endif
            @endif

        </div>

    </div>

</div>

@endsection
