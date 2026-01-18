@extends('layouts.admin')

@section('title', 'Coupons Management')

@section('content')

<div class="table-card">

    <!-- Search & Filter Section -->
    <div class="search-filter-card" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
        <form method="GET" action="{{ route('coupons.index') }}">

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                <!-- Search -->
                <div>
                    <label>Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by code..."
                        style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                </div>

                <!-- Status Filter -->
                <div>
                    <label>Status</label>
                    <select name="is_active" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label>Discount Type</label>
                    <select name="discount_type" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="">All Types</option>
                        <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div>
                    <label>Sort Direction</label>
                    <select name="sort_dir" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

            </div>

            <div style="margin-top:15px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('coupons.index') }}"
                    style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ route('coupons.create') }}" class="btn btn-primary" style="margin-left:auto;">
                    <i class="fas fa-plus"></i> Create New Coupon
                </a>
            </div>

        </form>
    </div>

    @if (session('success'))
        <div class="alert-auto-hide">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        <style>
            .alert-auto-hide {
                background: #d1fae5;
                color: #065f46;
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 20px;
                border: 1px solid #a7f3d0;
                animation: fade 3s forwards;
            }
            @keyframes fade {
                0%, 60% { opacity: 1; }
                100% { opacity: 0; display: none; }
            }
        </style>
    @endif

    @if (session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:6px;margin-bottom:20px;border:1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="table-header" style="margin-bottom:20px;">
        <h3>Coupons List ({{ $coupons->total() }} coupons)</h3>
    </div>

    <!-- Coupons Table -->
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Valid Until</th>
                <th>Usage</th>
                <th>Min Order</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Actions</th>
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
                                <i class="fas fa-percent"></i> Percentage
                            </span>
                        @else
                            <span class="badge" style="background:#d1fae5;color:#065f46;">
                                <i class="fas fa-dollar-sign"></i> Fixed
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
                        @if($coupon->end_date->isPast())
                            <span class="badge danger" style="background:#fee2e2;color:#991b1b;font-size:11px;">Expired</span>
                        @elseif($coupon->end_date->isToday())
                            <span class="badge warning" style="background:#fef3c7;color:#92400e;font-size:11px;">Expires Today</span>
                        @endif
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
                        {{ $coupon->min_order_amount ? '$' . number_format($coupon->min_order_amount, 2) : 'None' }}
                    </td>
                    <td>
                        @if($coupon->is_active && $coupon->isValid())
                            <span class="badge success" style="background:#d1fae5;color:#065f46;">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        @elseif(!$coupon->is_active)
                            <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                                <i class="fas fa-pause-circle"></i> Inactive
                            </span>
                        @else
                            <span class="badge danger" style="background:#fee2e2;color:#991b1b;">
                                <i class="fas fa-times-circle"></i> Expired
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $coupon->creator->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('coupons.show', $coupon) }}" class="action-btn" style="background:#eff6ff;color:#1e40af;">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('coupons.edit', $coupon) }}" class="action-btn edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('coupons.toggle', $coupon) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="action-btn"
                                    style="background:{{ $coupon->is_active ? '#fef3c7' : '#d1fae5' }};color:{{ $coupon->is_active ? '#92400e' : '#065f46' }};">
                                    <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                    {{ $coupon->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            @if($coupon->used_count == 0)
                                <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete">
                                        <i class="fas fa-trash"></i> Delete
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
                        No coupons found
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
