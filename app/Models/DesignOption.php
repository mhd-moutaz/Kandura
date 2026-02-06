<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DesignOption extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'type', 'hex_color'];

    public $translatable = ['name'];

    public function designs()
    {
        return $this->belongsToMany(Design::class, 'design_option_selection');
    }
    public function orderItems()
    {
        return $this->belongsToMany(OrderItems::class, 'order_item_design_option', 'order_item_id', 'design_option_id');
    }

    /**
     * Check if this option is a color type
     */
    public function isColorType(): bool
    {
        return $this->type === 'color';
    }

    /**
     * Get formatted hex color with fallback
     */
    public function getColorPreviewAttribute(): ?string
    {
        return $this->hex_color ?? null;
    }
}
