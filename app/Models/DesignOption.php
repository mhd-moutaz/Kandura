<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;

class DesignOption extends Model
{
    use HasTranslations;
    protected $fillable = ['name', 'type'];
    public $translatable = ['name'];
    protected $casts = [
        'name' => 'array',
    ];
    public function designs()
    {
        return $this->belongsToMany(Design::class, 'design_option_selection');
    }
}
