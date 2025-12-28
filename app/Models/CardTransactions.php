<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTransactions extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
