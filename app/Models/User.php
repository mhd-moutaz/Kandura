<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\UserRoleEnum;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'profile_image',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * تحديد الـ guard المناسب حسب دور المستخدم
     */
    protected function getDefaultGuardName(): string
    {
        // إذا اليوزر عادي، استخدم api guard
        if ($this->role === UserRoleEnum::USER) {
            return 'api';
        }
        // وإلا استخدم web guard (للـ admin و super_admin)
        return 'web';
    }


    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }


    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function cardTransactions()
    {
        return $this->hasMany(CardTransactions::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet ?? $this->wallet()->create(['balance' => 0]);
    }

    public function scopeFilter($query, array $filters): void
    {
        // 1. البحث (Search)
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });
        // 2. الفلترة (Filters)
        $query->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
            $q->where('is_active', $filters['is_active']);
        });

        // فلترة حسب الدور (Role)
        $query->when($filters['role'] ?? null, function ($q, $role) {
            $q->where('role', $role);
        });

        // 3. الترتيب (Sorting)
        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_dir'] ?? 'desc';

        // حماية: تأكد أن العمود موجود لمنع أخطاء SQL
        if (in_array($sortColumn, ['id', 'created_at', 'name', 'email'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }
    }
}
