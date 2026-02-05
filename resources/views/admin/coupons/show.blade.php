@extends('layouts.admin')

@section('title', __('messages.coupon_details') . ' - ' . $coupon->code)

@push('styles')
<link href="{{ asset('css/admin/coupons.css') }}" rel="stylesheet">
@endpush

@section('content')

<div style="max-width:1400px;margin:0 auto;">

    <!-- Back Button -->
    <div style="margin-bottom:20px;">
        <a href="{{ route('coupons.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;padding:8px 14px;background:white;border-radius:6px;border:1px solid #e2e8f0;transition:all 0.2s;font-size:14px;">
            <i class="fas fa-arrow-left"></i>
            <span>{{ __('messages.back_to_coupons') }}</span>
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

            <!-- Coupon Header -->
            <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:20px;">
                    <div>
                        <h2 style="font-size:24px;font-weight:700;color:#1f2937;margin-bottom:8px;">
                            <i class="fas fa-ticket-alt" style="color:#3b82f6;"></i> {{ $coupon->code }}
                        </h2>
                        <p style="color:#6b7280;font-size:14px;">
                            <i class="fas fa-clock"></i> {{ __('messages.created_at') }}: {{ $coupon->created_at->format('F d, Y - H:i') }}
                        </p>
                    </div>
                    <span style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:24px;font-size:14px;font-weight:600;background:{{ $coupon->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $coupon->is_active ? '#065f46' : '#991b1b' }};">
                        <i class="fas fa-{{ $coupon->is_active ? 'check-circle' : 'times-circle' }}"></i>
                        {{ $coupon->is_active ? __('messages.active') : __('messages.inactive') }}
                    </span>
                </div>

                <!-- Quick Stats -->
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
                    <div style="background:#f0fdf4;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.discount') }}</div>
                        <div style="font-size:24px;font-weight:700;color:#10b981;">
                            @if($coupon->discount_type == 'percentage')
                                {{ $coupon->discount_value }}%
                            @else
                                ${{ number_format($coupon->discount_value, 2) }}
                            @endif
                        </div>
                        <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ ucfirst(__('messages.' . $coupon->discount_type)) }}</div>
                    </div>
                    <div style="background:#dbeafe;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.used') }}</div>
                        <div style="font-size:24px;font-weight:700;color:#1e40af;">{{ $coupon->used_count }}</div>
                        <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ __('messages.of') }} {{ $coupon->usage_limit }}</div>
                    </div>
                    <div style="background:#fef3c7;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.min_order') }}</div>
                        <div style="font-size:24px;font-weight:700;color:#92400e;">
                            @if($coupon->min_order_amount)
                                ${{ number_format($coupon->min_order_amount, 2) }}
                            @else
                                <span style="font-size:16px;">{{ __('messages.no_minimum') }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="background:#e0e7ff;padding:16px;border-radius:8px;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">{{ __('messages.remaining') }}</div>
                        <div style="font-size:24px;font-weight:700;color:#3730a3;">
                            {{ $coupon->usage_limit - $coupon->used_count }}
                        </div>
                        <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ __('messages.uses_left') }}</div>
                    </div>
                </div>
            </div>

            <!-- Coupon Details -->
            <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-info-circle" style="color:#3b82f6;"></i>
                    {{ __('messages.coupon_details') }}
                </h3>

                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
                    <!-- Code -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.code') }}</div>
                        <div style="font-size:18px;font-weight:600;color:#1f2937;font-family:monospace;">{{ $coupon->code }}</div>
                    </div>

                    <!-- Discount Type -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.discount_type') }}</div>
                        <div style="font-size:16px;font-weight:600;color:#1f2937;text-transform:capitalize;">
                            <i class="fas fa-{{ $coupon->discount_type == 'percentage' ? 'percent' : 'dollar-sign' }}"></i>
                            {{ __('messages.' . $coupon->discount_type) }}
                        </div>
                    </div>

                    <!-- Discount Value -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.discount_value') }}</div>
                        <div style="font-size:20px;font-weight:700;color:#10b981;">
                            @if($coupon->discount_type == 'percentage')
                                {{ $coupon->discount_value }}%
                            @else
                                ${{ number_format($coupon->discount_value, 2) }}
                            @endif
                        </div>
                    </div>

                    <!-- Min Order Amount -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.minimum_order_amount') }}</div>
                        <div style="font-size:20px;font-weight:700;color:#f59e0b;">
                            @if($coupon->min_order_amount)
                                ${{ number_format($coupon->min_order_amount, 2) }}
                            @else
                                <span style="font-size:14px;color:#6b7280;">{{ __('messages.no_minimum') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.start_date') }}</div>
                        <div style="font-size:16px;font-weight:600;color:#1f2937;">
                            @if($coupon->start_date)
                                <i class="fas fa-calendar-check"></i>
                                {{ $coupon->start_date->format('M d, Y') }}
                            @else
                                <span style="font-size:14px;color:#6b7280;">{{ __('messages.immediately') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- End Date -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.end_date') }}</div>
                        <div style="font-size:16px;font-weight:600;color:#1f2937;">
                            <i class="fas fa-calendar-times"></i>
                            {{ $coupon->end_date->format('M d, Y') }}
                        </div>
                        @php
                            $daysRemaining = (int)now()->diffInDays($coupon->end_date, false);
                        @endphp
                        @if($daysRemaining > 0)
                            <div style="font-size:11px;color:#10b981;margin-top:4px;">
                                <i class="fas fa-clock"></i> {{ __('messages.days_remaining', ['days' => $daysRemaining]) }}
                            </div>
                        @elseif($daysRemaining == 0)
                            <div style="font-size:11px;color:#f59e0b;margin-top:4px;">
                                <i class="fas fa-exclamation-triangle"></i> {{ __('messages.expires_today') }}
                            </div>
                        @else
                            <div style="font-size:11px;color:#dc2626;margin-top:4px;">
                                <i class="fas fa-times-circle"></i> {{ __('messages.expired') }}
                            </div>
                        @endif
                    </div>

                    <!-- Usage Limit -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.usage_limit') }}</div>
                        <div style="font-size:20px;font-weight:700;color:#3b82f6;">{{ $coupon->usage_limit }}</div>
                    </div>

                    <!-- Used Count -->
                    <div style="padding:16px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;font-weight:500;">{{ __('messages.times_used') }}</div>
                        <div style="font-size:20px;font-weight:700;color:#8b5cf6;">{{ $coupon->used_count }}</div>
                        @php
                            $usagePercentage = $coupon->usage_limit > 0 ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0;
                        @endphp
                        <div style="margin-top:8px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                            <div style="height:100%;background:#8b5cf6;width:{{ $usagePercentage }}%;transition:width 0.3s;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-history" style="color:#3b82f6;"></i>
                    {{ __('messages.usage_history') }}
                    <span style="padding:4px 10px;background:#e0e7ff;color:#3730a3;border-radius:12px;font-size:13px;font-weight:600;">
                        {{ $coupon->usages->count() }}
                    </span>
                </h3>

                @if($coupon->usages->count() > 0)
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:separate;border-spacing:0 8px;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;border-radius:8px 0 0 8px;">{{ __('messages.user') }}</th>
                                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.order_id') }}</th>
                                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.discount_amount') }}</th>
                                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.used_date') }}</th>
                                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;border-radius:0 8px 8px 0;">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupon->usages as $usage)
                                    <tr style="background:#f8fafc;transition:all 0.2s;">
                                        <td style="padding:14px;border-radius:8px 0 0 8px;">
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <div style="width:36px;height:36px;border-radius:50%;background:#5a67d8;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:600;">
                                                    {{ substr($usage->user->name ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div style="font-size:14px;font-weight:600;color:#1f2937;">{{ $usage->user->name ?? 'N/A' }}</div>
                                                    <div style="font-size:12px;color:#6b7280;">{{ $usage->user->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding:14px;">
                                            <span style="font-family:monospace;font-size:14px;font-weight:600;color:#3b82f6;">
                                                #{{ $usage->order_id }}
                                            </span>
                                        </td>
                                        <td style="padding:14px;">
                                            <span style="font-size:16px;font-weight:700;color:#10b981;">
                                                ${{ number_format($usage->discount_amount, 2) }}
                                            </span>
                                        </td>
                                        <td style="padding:14px;">
                                            <div style="font-size:13px;color:#6b7280;">
                                                <i class="fas fa-calendar"></i>
                                                {{ $usage->created_at->format('M d, Y') }}
                                            </div>
                                            <div style="font-size:11px;color:#9ca3af;">
                                                {{ $usage->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td style="padding:14px;border-radius:0 8px 8px 0;">
                                            @if($usage->order)
                                                <a href="{{ route('orders.show', $usage->order_id) }}"
                                                   style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#3b82f6;color:white;text-decoration:none;border-radius:6px;font-size:13px;font-weight:500;transition:all 0.2s;">
                                                    <i class="fas fa-eye"></i>
                                                    {{ __('messages.view_order') }}
                                                </a>
                                            @else
                                                <span style="color:#9ca3af;font-size:13px;">{{ __('messages.n_a') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align:center;padding:40px 20px;">
                        <i class="fas fa-inbox" style="font-size:48px;color:#d1d5db;margin-bottom:12px;"></i>
                        <h4 style="font-size:16px;font-weight:600;color:#6b7280;margin-bottom:6px;">{{ __('messages.no_usage_history') }}</h4>
                        <p style="font-size:14px;color:#9ca3af;">{{ __('messages.coupon_not_used_yet') }}</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Sidebar -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- Quick Actions -->
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-bolt" style="color:#3b82f6;"></i>
                    {{ __('messages.quick_actions') }}
                </h3>

                <div style="display:flex;flex-direction:column;gap:10px;">
                    <!-- Edit Button -->
                    @can('update coupon')
                    <a href="{{ route('coupons.edit', $coupon) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:#3b82f6;color:white;text-decoration:none;border-radius:8px;font-size:14px;font-weight:500;transition:all 0.2s;">
                        <i class="fas fa-edit"></i>
                        {{ __('messages.edit_coupon') }}
                    </a>
                    @endcan

                    <!-- Toggle Status -->
                    @can('update coupon')
                    <form action="{{ route('coupons.toggle', $coupon) }}" method="POST">
                        @csrf
                        <button type="submit"
                                style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:{{ $coupon->is_active ? '#f59e0b' : '#10b981' }};color:white;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;transition:all 0.2s;">
                            <i class="fas fa-{{ $coupon->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                            {{ $coupon->is_active ? __('messages.deactivate') : __('messages.activate') }}
                        </button>
                    </form>
                    @endcan

                    <!-- Delete Button -->
                    @can('delete coupon')
                    @if($coupon->used_count == 0)
                    <form action="{{ route('coupons.destroy', $coupon) }}" method="POST"
                          onsubmit="return confirm('{{ __('messages.delete_coupon_permanent') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:#dc2626;color:white;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;transition:all 0.2s;">
                            <i class="fas fa-trash-alt"></i>
                            {{ __('messages.delete') }}
                        </button>
                    </form>
                    @else
                    <div style="padding:12px;background:#fee2e2;color:#991b1b;border-radius:8px;font-size:13px;text-align:center;">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('messages.cannot_delete_used_coupon') }}
                    </div>
                    @endif
                    @endcan
                </div>
            </div>

            <!-- Creator Info -->
            @if($coupon->creator)
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-user-shield" style="color:#3b82f6;"></i>
                    {{ __('messages.created_by') }}
                </h3>

                <div style="display:flex;align-items:center;gap:12px;padding:12px;background:#f8fafc;border-radius:8px;">
                    <div style="width:48px;height:48px;border-radius:50%;background:#5a67d8;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:600;">
                        {{ substr($coupon->creator->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:600;color:#1f2937;">{{ $coupon->creator->name ?? 'N/A' }}</div>
                        <div style="font-size:12px;color:#6b7280;">{{ $coupon->creator->email ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistics -->
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-chart-line" style="color:#3b82f6;"></i>
                    {{ __('messages.statistics') }}
                </h3>

                <div style="display:flex;flex-direction:column;gap:12px;">
                    <!-- Usage Percentage -->
                    <div style="padding:12px;background:#f8fafc;border-radius:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <span style="font-size:13px;color:#6b7280;font-weight:500;">{{ __('messages.usage_rate') }}</span>
                            <span style="font-size:14px;font-weight:700;color:#8b5cf6;">
                                {{ $coupon->usage_limit > 0 ? number_format(($coupon->used_count / $coupon->usage_limit) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;background:#8b5cf6;width:{{ $coupon->usage_limit > 0 ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0 }}%;transition:width 0.3s;"></div>
                        </div>
                    </div>

                    <!-- Total Discount Given -->
                    @php
                        $totalDiscountGiven = $coupon->usages->sum('discount_amount');
                    @endphp
                    <div style="padding:12px;background:#f8fafc;border-radius:8px;">
                        <div style="font-size:13px;color:#6b7280;margin-bottom:4px;font-weight:500;">{{ __('messages.total_discount_given') }}</div>
                        <div style="font-size:20px;font-weight:700;color:#10b981;">
                            ${{ number_format($totalDiscountGiven, 2) }}
                        </div>
                    </div>

                    <!-- Average Discount -->
                    @php
                        $avgDiscount = $coupon->usages->count() > 0 ? $totalDiscountGiven / $coupon->usages->count() : 0;
                    @endphp
                    <div style="padding:12px;background:#f8fafc;border-radius:8px;">
                        <div style="font-size:13px;color:#6b7280;margin-bottom:4px;font-weight:500;">{{ __('messages.average_discount') }}</div>
                        <div style="font-size:20px;font-weight:700;color:#f59e0b;">
                            ${{ number_format($avgDiscount, 2) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
