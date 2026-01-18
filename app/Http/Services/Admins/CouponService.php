<?php

namespace App\Http\Services\Admins;

use App\Models\Coupon;

class CouponService
{
    public function index(array $filters)
    {
        $query = Coupon::with('creator');

        // Search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('code', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filter by active status
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by discount type
        if (!empty($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        // Sort
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy('created_at', $sortDir);

        return $query->paginate(10)->withQueryString();
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
