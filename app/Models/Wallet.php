<?php
// app/Models/Wallet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * إضافة رصيد للمحفظة
     */
    public function deposit(float $amount, string $description = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        $this->refresh();

        return $this->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Deposit to wallet',
            'metadata' => $metadata,
        ]);
    }

    /**
     * سحب رصيد من المحفظة
     */
    public function withdraw(float $amount, string $description = null, array $metadata = []): WalletTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        $this->refresh();

        return $this->transactions()->create([
            'type' => 'withdraw',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Withdraw from wallet',
            'metadata' => $metadata,
        ]);
    }

    /**
     * الدفع من المحفظة
     */
    public function pay(float $amount, string $description = null, array $metadata = []): WalletTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        $this->refresh();

        return $this->transactions()->create([
            'type' => 'payment',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Payment from wallet',
            'metadata' => $metadata,
        ]);
    }

    /**
     * استرجاع مبلغ للمحفظة
     */
    public function refund(float $amount, string $description = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        $this->refresh();

        return $this->transactions()->create([
            'type' => 'refund',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Refund to wallet',
            'metadata' => $metadata,
        ]);
    }
}
