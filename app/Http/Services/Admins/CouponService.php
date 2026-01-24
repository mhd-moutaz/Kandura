<?php

namespace App\Http\Services\Admins;

use App\Models\Coupon;

class CouponService
{
    public function index(array $filters)
    {
        return Coupon::with('creator')
            ->filter($filters)
            ->paginate(10)
            ->withQueryString();
    }

    public function store(array $data)
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data)
    {
        $coupon->update($data);
        return $coupon;
    }

    public function getStatistics()
    {
        return [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'expired_coupons' => Coupon::where('end_date', '<', now())->count(),
            'total_usage' => Coupon::sum('used_count'),
        ];
    }
}
