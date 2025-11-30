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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
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

        // 3. الترتيب (Sorting)
        $sortColumn = $filters['sort_by'] ?? 'id';
        $sortDirection = $filters['sort_dir'] ?? 'asc';

        // حماية: تأكد أن العمود موجود لمنع أخطاء SQL
        if (in_array($sortColumn, ['id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }
        
    }
}
