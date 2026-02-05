@extends('layouts.admin')

@section('title', __('messages.coupons_management'))

@push('styles')
<link href="{{ asset('css/admin/coupons.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="table-card">

    <!-- Search & Filter Section -->
    <div class="search-filter-card" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
        <form method="GET" action="{{ route('coupons.index') }}">

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                <!-- Search -->
                <div>
                    <label>{{ __('messages.search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('messages.search_by_code') }}"
                        style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                </div>

                <!-- Status Filter -->
                <div>
                    <label>{{ __('messages.status') }}</label>
                    <select name="is_active" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">{{ __('messages.all_status') }}</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        <option value="expired" {{ request('is_active') == 'expired' ? 'selected' : '' }}>{{ __('messages.expired') }}</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label>{{ __('messages.discount_type') }}</label>
                    <select name="discount_type" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
                        <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed_amount') }}</option>
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
                <a href="{{ route('coupons.index') }}"
                    style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                    <i class="fas fa-redo"></i> {{ __('messages.reset') }}
                </a>
                <a href="{{ route('coupons.create') }}" class="btn btn-primary" style="margin-left:auto;">
                    <i class="fas fa-plus"></i> {{ __('messages.create_new_coupon') }}
                </a>
            </div>

        </form>
    </div>

    @if (session('success'))
        <div class="alert-auto-hide">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:6px;margin-bottom:20px;border:1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="table-header" style="margin-bottom:20px;">
        <h3>{{ __('messages.coupons_list') }} ({{ __('messages.coupons_count', ['count' => $coupons->total()]) }})</h3>
    </div>

    <!-- Coupons Table -->
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.code') }}</th>
                <th>{{ __('messages.type') }}</th>
                <th>{{ __('messages.value') }}</th>
                <th>{{ __('messages.valid_until') }}</th>
                <th>{{ __('messages.usage') }}</th>
                <th>{{ __('messages.min_order') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.created_by') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($coupons as $coupon)
                <tr>
                    <td>
                        <strong style="font-family:monospace;color:#3b82f6;">{{ $coupon->code }}</strong>
                    </td>
                    <td>
                        @if($coupon->discount_type == 'percentage')
                            <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                <i class="fas fa-percent"></i> {{ __('messages.percentage') }}
                            </span>
                        @else
                            <span class="badge" style="background:#d1fae5;color:#065f46;">
                                <i class="fas fa-dollar-sign"></i> {{ __('messages.fixed') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong style="color:#10b981;">
                            {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : '$' . number_format($coupon->discount_value, 2) }}
                        </strong>
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $coupon->end_date->format('Y-m-d') }}</div>
                        {{-- @if($coupon->end_date->isPast())
                            <span class="badge danger" style="background:#fee2e2;color:#991b1b;font-size:11px;">Expired</span>
                        @elseif($coupon->end_date->isToday())
                            <span class="badge warning" style="background:#fef3c7;color:#92400e;font-size:11px;">Expires Today</span>
                        @endif --}}
                    </td>
                    <td>
                        <div style="font-size:13px;">
                            <strong>{{ $coupon->used_count }}</strong> / {{ $coupon->usage_limit }}
                        </div>
                        <div style="background:#e5e7eb;height:4px;border-radius:2px;margin-top:4px;">
                            <div style="background:#3b82f6;height:100%;width:{{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%;border-radius:2px;"></div>
                        </div>
                    </td>
                    <td>
                        {{ $coupon->min_order_amount ? '$' . number_format($coupon->min_order_amount, 2) : __('messages.none') }}
                    </td>
                    <td>
                        @if($coupon->is_active && $coupon->isValid())
                            <span class="badge success" style="background:#d1fae5;color:#065f46;">
                                <i class="fas fa-check-circle"></i> {{ __('messages.active') }}
                            </span>
                        @elseif(!$coupon->is_active)
                            <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                                <i class="fas fa-pause-circle"></i> {{ __('messages.inactive') }}
                            </span>
                        @else
                            <span class="badge danger" style="background:#fee2e2;color:#991b1b;">
                                <i class="fas fa-times-circle"></i> {{ __('messages.expired') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $coupon->creator->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('coupons.show', $coupon) }}" class="action-btn" style="background:#eff6ff;color:#1e40af;">
                                <i class="fas fa-eye"></i> {{ __('messages.view') }}
                            </a>
                            <a href="{{ route('coupons.edit', $coupon) }}" class="action-btn edit">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                            <form action="{{ route('coupons.toggle', $coupon) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="action-btn"
                                    style="background:{{ $coupon->is_active ? '#fef3c7' : '#d1fae5' }};color:{{ $coupon->is_active ? '#92400e' : '#065f46' }};">
                                    <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                    {{ $coupon->is_active ? __('messages.disable') : __('messages.enable') }}
                                </button>
                            </form>
                            @if($coupon->used_count == 0)
                                <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('{{ __('messages.delete_coupon_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;">
                        <i class="fas fa-ticket-alt" style="font-size:48px;margin-bottom:10px;display:block;"></i>
                        {{ __('messages.no_coupons_found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if ($coupons->hasPages())
        <div class="pagination" style="margin-top:30px;">
            {{ $coupons->links() }}
        </div>
    @endif

</div>

@endsection
