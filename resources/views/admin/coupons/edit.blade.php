@extends('layouts.admin')

@section('title', 'Edit Coupon - ' . $coupon->code)

@push('styles')
<link href="{{ asset('css/admin/coupons.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>Edit Coupon: {{ $coupon->code }}</h2>
        <p style="color:#6b7280;font-size:14px;margin-top:4px;">
            <i class="fas fa-clock"></i> Created on {{ $coupon->created_at->format('M d, Y') }}
        </p>
    </div>
    <div class="header-right">
        <a href="{{ route('coupons.show', $coupon) }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-eye"></i> View Details
        </a>
        <a href="{{ route('coupons.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if (session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #10b981;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #dc2626;">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
    </div>
@endif

<!-- Form Card -->
<div class="table-card">
    <!-- Usage Warning -->
    @if($coupon->used_count > 0)
        <div style="background:#fef3c7;border:1px solid #fbbf24;border-radius:8px;padding:14px;display:flex;gap:12px;align-items:start;margin-bottom:20px;">
            <i class="fas fa-exclamation-triangle" style="color:#f59e0b;margin-top:2px;font-size:20px;"></i>
            <div style="color:#92400e;font-size:13px;line-height:1.6;">
                <strong>Warning:</strong> This coupon has been used <strong>{{ $coupon->used_count }}</strong> time(s).
                Changing the discount value or type may affect the consistency of past orders.
            </div>
        </div>
    @endif

    <form action="{{ route('coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display:grid;gap:20px;">

            <!-- Coupon Code -->
            <div>
                <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                    Coupon Code <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required
                    placeholder="e.g., SAVE20, WELCOME10"
                    style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;text-transform:uppercase;">
                @error('code')
                    <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                @enderror
                <span style="font-size:12px;color:#6b7280;margin-top:4px;display:block;">
                    <i class="fas fa-info-circle"></i> Code will be automatically converted to uppercase
                </span>
            </div>

            <!-- Discount Type and Value -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Discount Type <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="discount_type" id="discount_type" required
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                        <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                    @error('discount_type')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Discount Value <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}"
                        required step="0.01" min="0" max="{{ old('discount_type', $coupon->discount_type) == 'percentage' ? '100' : '' }}"
                        placeholder="Enter value"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('discount_value')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                    <span id="discount_hint" style="font-size:12px;color:#6b7280;margin-top:4px;display:block;">
                        <i class="fas fa-info-circle"></i>
                        @if(old('discount_type', $coupon->discount_type) == 'percentage')
                            Maximum value is 100%
                        @else
                            Enter discount amount in dollars
                        @endif
                    </span>
                </div>
            </div>

            <!-- Dates -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Start Date (Optional)
                    </label>
                    <input type="datetime-local" name="start_date"
                        value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d\TH:i') : '') }}"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('start_date')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                    <span style="font-size:12px;color:#6b7280;margin-top:4px;display:block;">
                        <i class="fas fa-info-circle"></i> If not set, coupon is active immediately
                    </span>
                </div>

                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        End Date <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="datetime-local" name="end_date"
                        value="{{ old('end_date', $coupon->end_date->format('Y-m-d\TH:i')) }}" required
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('end_date')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Usage Limit and Min Order -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Usage Limit <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
                        required min="{{ $coupon->used_count }}"
                        placeholder="e.g., 100"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('usage_limit')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                    <span style="font-size:12px;color:#6b7280;margin-top:4px;display:block;">
                        <i class="fas fa-info-circle"></i> Cannot be less than current usage ({{ $coupon->used_count }})
                    </span>
                </div>

                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Minimum Order Amount (Optional)
                    </label>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                        step="0.01" min="0"
                        placeholder="e.g., 50.00"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('min_order_amount')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                    <span style="font-size:12px;color:#6b7280;margin-top:4px;display:block;">
                        <i class="fas fa-info-circle"></i> Minimum order value required to use this coupon
                    </span>
                </div>
            </div>

            <!-- Current Usage Stats -->
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;">
                <h4 style="font-size:14px;font-weight:600;color:#0369a1;margin:0 0 12px 0;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-chart-bar"></i> Current Usage Statistics
                </h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;">
                    <div style="background:white;padding:12px;border-radius:6px;text-align:center;">
                        <div style="font-size:11px;color:#6b7280;margin-bottom:4px;">Used</div>
                        <div style="font-size:20px;font-weight:700;color:#0284c7;">{{ $coupon->used_count }}</div>
                    </div>
                    <div style="background:white;padding:12px;border-radius:6px;text-align:center;">
                        <div style="font-size:11px;color:#6b7280;margin-bottom:4px;">Remaining</div>
                        <div style="font-size:20px;font-weight:700;color:#10b981;">{{ $coupon->usage_limit - $coupon->used_count }}</div>
                    </div>
                    <div style="background:white;padding:12px;border-radius:6px;text-align:center;">
                        <div style="font-size:11px;color:#6b7280;margin-bottom:4px;">Usage Rate</div>
                        <div style="font-size:20px;font-weight:700;color:#8b5cf6;">
                            {{ $coupon->usage_limit > 0 ? number_format(($coupon->used_count / $coupon->usage_limit) * 100, 0) : 0 }}%
                        </div>
                    </div>
                    <div style="background:white;padding:12px;border-radius:6px;text-align:center;">
                        <div style="font-size:11px;color:#6b7280;margin-bottom:4px;">Total Discount Given</div>
                        <div style="font-size:20px;font-weight:700;color:#f59e0b;">
                            ${{ number_format($coupon->usages->sum('discount_amount'), 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div style="background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e5e7eb;">
                <label style="display:flex;align-items:center;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                        style="width:20px;height:20px;margin-right:10px;">
                    <div>
                        <div style="font-weight:500;color:#374151;">Coupon Status</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                            Enable or disable this coupon for user redemption
                        </div>
                    </div>
                </label>
            </div>

            <!-- Information Box -->
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:14px;display:flex;gap:12px;align-items:start;">
                <i class="fas fa-info-circle" style="color:#3b82f6;margin-top:2px;"></i>
                <div style="color:#1e40af;font-size:13px;line-height:1.6;">
                    <strong>Important Notes:</strong>
                    <ul style="margin:8px 0 0 20px;padding:0;">
                        <li>Each user can only use a coupon once</li>
                        <li>For fixed amount coupons, order total must be ≥ discount value</li>
                        <li>Usage limit cannot be less than the current usage count</li>
                        <li>Changes to dates will not affect orders that have already used this coupon</li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="display:flex;gap:12px;padding-top:20px;border-top:1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-save"></i> Update Coupon
                </button>
                <a href="{{ route('coupons.show', $coupon) }}" class="btn" style="background:#f3f4f6;color:#4b5563;flex:1;text-align:center;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/coupons.js') }}"></script>
@endpush
