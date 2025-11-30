<?php

namespace App\Models;

use App\Enum\UserRoleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'district',
        'street',
        'house_number',
        'notes',
        'Langitude',
        'Latitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeFilter($query, array $filters): void
    {
        $user = User::find(Auth::id());
        if ($user->hasRole(UserRoleEnum::USER)) {
            $query->where('user_id', $user->id);
        }


        // 1. البحث (Search)
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('street', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('house_number', 'like', "%{$search}%")
                    ->orWhereHas('city', function ($cityQ) use ($search) {
                        // نعيد استخدام سكوب البحث الموجود في مودل City
                        $cityQ->byName($search);
                    })
                    ->orWhereHas('user', function ($userQ) use ($search) {
                        // البحث في اسم المستخدم أو رقم الهاتف
                        $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        });
        // 2. الفلترة (Filters)
        $query->when($filters['city'] ?? null, function ($q, $city) {
            $q->whereHas('city', function ($cityQ) use ($city) {
                $cityQ->byName($city);
            });
        });
        $query->when($filters['district'] ?? null, function ($q, $district) {
            $q->where('district', 'like', "%{$district}%");
        });
        $query->when($filters['street'] ?? null, function ($q, $street) {
            $q->where('street', 'like', "%{$street}%");
        });
        $query->when($filters['house_number'] ?? null, function ($q, $houseNumber) {
            $q->where('house_number', 'like', "%{$houseNumber}%");
        });

        // 3. الترتيب (Sorting)
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_dir'] ?? 'asc';

        // حماية: تأكد أن العمود موجود لمنع أخطاء SQL
        if (in_array($sortColumn, ['id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }
    }

}
