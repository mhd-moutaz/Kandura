<?php

namespace App\Http\Controllers\Admins;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Admins\CouponService;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_active', 'discount_type', 'sort_dir']);
        $coupons = $this->couponService->index($filters);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('The discount value cannot exceed 100% for percentage discounts.');
                    }
                },
            ],
            'start_date' => 'nullable|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'required|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $this->couponService->store($validated);

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon created successfully');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.user', 'usages.order']);
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('The discount value cannot exceed 100% for percentage discounts.');
                    }
                },
            ],
            'start_date' => 'nullable|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'required|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $this->couponService->update($coupon, $validated);

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon updated successfully');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->used_count > 0) {
            return redirect()->route('coupons.index')
                ->with('error', 'Cannot delete a coupon that has been used');
        }

        $coupon->delete();

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon deleted successfully');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return redirect()->back()
            ->with('success', 'Coupon status updated successfully');
    }
}
