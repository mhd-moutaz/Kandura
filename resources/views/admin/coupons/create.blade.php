@extends('layouts.admin')

@section('title', 'Create Coupon')

@push('styles')
<link href="{{ asset('css/admin/coupons.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>Create New Coupon</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('coupons.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="table-card">
    <form action="{{ route('coupons.store') }}" method="POST">
        @csrf

        <div style="display:grid;gap:20px;">

            <!-- Coupon Code -->
            <div>
                <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                    Coupon Code <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="code" value="{{ old('code') }}" required
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
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                    @error('discount_type')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Discount Value <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="number" name="discount_value" value="{{ old('discount_value') }}"
                        required step="0.01" min="0"
                        placeholder="Enter value"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('discount_value')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Dates -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Start Date (Optional)
                    </label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
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
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" required
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
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', 100) }}"
                        required min="1"
                        placeholder="e.g., 100"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                    @error('usage_limit')
                        <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                    <span style="font-size:12px;color:#6b7280;margin-top:4px;display:block;">
                        <i class="fas fa-info-circle"></i> Maximum number of times this coupon can be used
                    </span>
                </div>

                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;">
                        Minimum Order Amount (Optional)
                    </label>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount') }}"
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

            <!-- Active Status -->
            <div style="background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e5e7eb;">
                <label style="display:flex;align-items:center;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        style="width:20px;height:20px;margin-right:10px;">
                    <div>
                        <div style="font-weight:500;color:#374151;">Activate Coupon Immediately</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                            Coupon will be available for use as soon as it's created (if start date allows)
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
                        <li>Coupon code is case-insensitive (automatically converted to uppercase)</li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="display:flex;gap:12px;padding-top:20px;border-top:1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-save"></i> Create Coupon
                </button>
                <a href="{{ route('coupons.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;flex:1;text-align:center;">
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
