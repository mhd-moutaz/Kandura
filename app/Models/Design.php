<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

class Design extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'price',
        'state',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'state' => 'boolean',
        ];
    }

    public $translatable = ['name', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designImages()
    {
        return $this->hasMany(DesignImage::class);
    }

    public function measurements()
    {
        return $this->belongsToMany(Measurement::class, 'design_measurement');
    }

    public function designOptions()
    {
        return $this->belongsToMany(DesignOption::class, 'design_option_selection');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $isAdmin = Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin');

        // Filter by state: users see only active designs, admins see all
        $this->applyFilterByState($query, $isAdmin, $filters['state'] ?? null);

        $this->applySearch($query, $filters['search'] ?? null, $isAdmin);
        $this->applyFilterBySize($query, $filters['size'] ?? null);
        $this->applyFilterByPriceRange($query, $filters['min_price'] ?? null, $filters['max_price'] ?? null);
        $this->applyFilterByDesignOption($query, $filters['design_option'] ?? null);
        $this->applyFilterByCreator($query, $filters['creator'] ?? null);
        $this->applySorting($query, $filters['sort_by'] ?? 'created_at', $filters['sort_dir'] ?? 'asc');
    }

    private function applyFilterByState(Builder $query, bool $isAdmin, mixed $state): void
    {
        // If admin and specific state filter provided, use it
        if ($isAdmin && $state !== null && $state !== '') {
            $query->where('state', (bool) $state);
            return;
        }

        // If admin without filter, show all (no constraint)
        if ($isAdmin) {
            return;
        }

        // For regular users, only show active designs (state = true)
        $query->where('state', true);
    }

    private function applySearch(Builder $query, ?string $search, bool $includeUserName = false): void
    {
        if (empty($search)) {
            return;
        }

        $query->where(function ($q) use ($search, $includeUserName) {
            // البحث حسب ID إذا كان رقماً
            if (is_numeric($search)) {
                $q->where('id', $search);
            }

            // البحث في name و description
            $q->orWhere('name->ar', 'like', "%{$search}%")
                ->orWhere('name->en', 'like', "%{$search}%")
                ->orWhere('description->ar', 'like', "%{$search}%")
                ->orWhere('description->en', 'like', "%{$search}%");

            // إذا كان Admin، بحث في اسم المستخدم
            if ($includeUserName) {
                $q->orWhereHas('user', function ($userQ) use ($search) {
                    $userQ->where('name', 'like', "%{$search}%");
                });
            }
        });
    }
    private function applyFilterBySize(Builder $query, mixed $size): void
    {
        if (empty($size)) {
            return;
        }

        $query->whereHas('measurements', function ($q) use ($size) {
            $sizes = is_array($size) ? $size : [$size];
            $q->whereIn('measurements.size', $sizes);
        });
    }
    private function applyFilterByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice): void
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
    }
    private function applyFilterByDesignOption(Builder $query, mixed $designOption): void
    {
        if (empty($designOption)) {
            return;
        }

        $query->whereHas('designOptions', function ($q) use ($designOption) {
            $options = is_array($designOption) ? $designOption : [$designOption];

            $q->where(function ($subQ) use ($options) {
                foreach ($options as $option) {
                    $subQ->orWhere('design_options.type', 'like', "%{$option}%")
                        ->orWhere('design_options.name->ar', 'like', "%{$option}%")
                        ->orWhere('design_options.name->en', 'like', "%{$option}%");
                }
            });
        });
    }
    private function applyFilterByCreator(Builder $query, mixed $creator): void
    {
        if (empty($creator)) {
            return;
        }

        $creators = is_array($creator) ? $creator : [$creator];

        $query->whereIn('user_id', $creators);
    }
    private function applySorting(Builder $query, string $column, string $direction): void
    {
        $allowedColumns = ['id', 'name', 'price', 'created_at', 'updated_at'];

        if (in_array($column, $allowedColumns)) {
            $query->orderBy($column, $direction);
        }
    }
}
