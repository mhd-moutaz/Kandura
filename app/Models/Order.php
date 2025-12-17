<?php

namespace App\Models;

use App\Enum\StatusOrderEnum;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'total',
        'payment_method',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }
    public function scopeFilter($query, array $filters): void
    {
        $query->where('status', '!=', StatusOrderEnum::PENDING);
        // البحث
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('id', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQ) use ($search) {
                        $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        });

        // فلتر الحالة
        $query->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        });

        // فلتر طريقة الدفع
        $query->when($filters['payment_method'] ?? null, function ($q, $method) {
            $q->where('payment_method', $method);
        });

        // الترتيب
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        $query->orderBy('created_at', $sortDirection);
    }
}
