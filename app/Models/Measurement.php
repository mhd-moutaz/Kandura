<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    protected $fillable = [
        'size' // e.g., 'S', 'M', 'L', 'XL'
    ];
    public function designs()
    {
        return $this->belongsToMany(Design::class, 'design_measurement');
    }
}
