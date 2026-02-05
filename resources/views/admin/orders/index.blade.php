@extends('layouts.admin')

@section('title', __('messages.orders_management'))

@push('styles')
<link href="{{ asset('css/admin/orders.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="table-card">

    <!-- Search & Filter Section -->
    <div class="search-filter-card" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
        <form method="GET" action="{{ route('orders.index') }}" id="searchFilterForm">

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                <!-- Search -->
                <div>
                    <label>{{ __('messages.search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('messages.search_order') }}"
                        style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                </div>

                <!-- Status Filter -->
                <div>
                    <label>{{ __('messages.status') }}</label>
                    <select name="status" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">{{ __('messages.all_status') }}</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('messages.confirmed') }}</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('messages.processing') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label>{{ __('messages.payment_method') }}</label>
                    <select name="payment_method" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">{{ __('messages.all_methods') }}</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>{{ __('messages.card') }}</option>
                        <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>{{ __('messages.wallet') }}</option>
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

            </div>

            <div style="margin-top:15px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                </button>
                <a href="{{ route('orders.index') }}"
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
        <h3>{{ __('messages.orders_list') }} ({{ __('messages.orders_count', ['count' => $orders->total()]) }})</h3>
    </div>

    <!-- Orders Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;">
        @forelse ($orders as $order)
            <a href="{{ route('orders.show', $order->id) }}"
               style="text-decoration:none;color:inherit;display:block;transition:all 0.3s;"
               onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,0.12)';"
               onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">

                <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">

                    <!-- Header -->
                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid #f0f0f0;">
                        <div>
                            <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">{{ __('messages.order_id') }}</div>
                            <div style="font-size:18px;font-weight:700;color:#1f2937;">#{{ $order->id }}</div>
                        </div>
                        <div style="text-align:right;">
                            @php
                                $statusColors = [
                                    'confirmed' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                    'processing' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                    'completed' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                    'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                ];
                                $color = $statusColors[$order->status] ?? ['bg' => '#f3f4f6', 'text' => '#4b5563'];
                            @endphp
                            <span style="display:inline-block;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                                {{ __('messages.' . $order->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#5a67d8;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:600;">
                                {{ substr($order->user->name ?? 'N', 0, 1) }}
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:14px;font-weight:600;color:#1f2937;">{{ $order->user->name ?? 'N/A' }}</div>
                                <div style="font-size:12px;color:#6b7280;">{{ $order->user->phone ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div style="background:#f8fafc;padding:10px;border-radius:8px;">
                            <div style="font-size:11px;color:#6b7280;margin-bottom:4px;">{{ __('messages.items') }}</div>
                            <div style="font-size:16px;font-weight:700;color:#1f2937;">{{ $order->orderItems->count() }}</div>
                        </div>
                        <div style="background:#f8fafc;padding:10px;border-radius:8px;">
                            <div style="font-size:11px;color:#6b7280;margin-bottom:4px;">{{ __('messages.total') }}</div>
                            <div style="font-size:16px;font-weight:700;color:#10b981;">${{ number_format($order->total, 2) }}</div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div style="margin-bottom:16px;">
                        <div style="font-size:11px;color:#6b7280;margin-bottom:6px;">{{ __('messages.payment_method') }}</div>
                        <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#eff6ff;border-radius:6px;">
                            <i class="fas fa-{{ $order->payment_method == 'cash' ? 'money-bill-wave' : ($order->payment_method == 'card' ? 'credit-card' : 'wallet') }}" style="color:#3b82f6;font-size:12px;"></i>
                            <span style="font-size:12px;font-weight:500;color:#1e40af;">{{ __('messages.' . $order->payment_method) }}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f0f0f0;">
                        <div style="font-size:11px;color:#9ca3af;">
                            <i class="fas fa-clock"></i> {{ $order->created_at->format('M d, Y H:i') }}
                        </div>
                        <div style="color:#3b82f6;font-size:12px;font-weight:500;">
                            {{ __('messages.view_details') }} <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                </div>
            </a>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size:64px;color:#cbd5e0;margin-bottom:20px;"></i>
                <h3 style="color:#4a5568;font-size:20px;margin-bottom:10px;">{{ __('messages.no_orders_found') }}</h3>
                <p style="color:#9ca3af;font-size:14px;">{{ __('messages.no_orders_match') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($orders->hasPages())
        <div class="pagination" style="margin-top:30px;">
            {{ $orders->links() }}
        </div>
    @endif

</div>

@endsection
