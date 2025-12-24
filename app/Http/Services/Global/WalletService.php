<?php
namespace App\Http\Services\Global;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * الحصول على رصيد المحفظة
     */
    public function getBalance(User $user): float
    {
        $wallet = $user->getOrCreateWallet();
        return (float) $wallet->balance;
    }

    /**
     * الحصول على المعاملات
     */
    public function getTransactions(User $user, array $filters = [])
    {
        $wallet = $user->getOrCreateWallet();

        $query = $wallet->transactions()
            ->orderBy('created_at', 'desc');

        // تطبيق الفلاتر
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * إضافة رصيد
     */
    public function deposit(User $user, float $amount, string $description = 'Deposit to wallet', array $metadata = []): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $metadata) {
            $wallet = $user->getOrCreateWallet();
            return $wallet->deposit($amount, $description, $metadata);
        });
    }

    /**
     * سحب رصيد
     */
    public function withdraw(User $user, float $amount, string $description = 'Withdraw from wallet', array $metadata = []): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $metadata) {
            $wallet = $user->getOrCreateWallet();
            return $wallet->withdraw($amount, $description, $metadata);
        });
    }

    /**
     * الدفع من المحفظة
     */
    public function pay(User $user, float $amount, string $description = 'Payment from wallet', array $metadata = []): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $metadata) {
            $wallet = $user->getOrCreateWallet();
            return $wallet->pay($amount, $description, $metadata);
        });
    }

    /**
     * استرجاع مبلغ
     */
    public function refund(User $user, float $amount, string $description = 'Refund to wallet', array $metadata = []): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $metadata) {
            $wallet = $user->getOrCreateWallet();
            return $wallet->refund($amount, $description, $metadata);
        });
    }

    /**
     * التحقق من كفاية الرصيد
     */
    public function hasEnoughBalance(User $user, float $amount): bool
    {
        $wallet = $user->getOrCreateWallet();
        return $wallet->balance >= $amount;
    }
}
