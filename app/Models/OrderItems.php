<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $fillable = [
        'order_id',
        'design_id',
        'measurement_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function measurement()
    {
        return $this->belongsTo(Measurement::class);
    }

    public function designOptions()
    {
        return $this->belongsToMany(DesignOption::class, 'order_item_design_option');
    }
}
