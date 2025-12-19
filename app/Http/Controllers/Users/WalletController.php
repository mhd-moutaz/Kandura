<?php
// app/Http/Controllers/Users/WalletController.php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\WalletService;
use App\Http\Resources\Users\WalletResource;
use App\Http\Resources\Users\WalletTransactionResource;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * الحصول على رصيد المحفظة
     */
    public function getBalance(Request $request)
    {
        $balance = $this->walletService->getBalance($request->user());

        return $this->success([
            'balance' => $balance,
            'formatted_balance' => '$' . number_format($balance, 2)
        ], 'Wallet balance retrieved successfully');
    }

    /**
     * الحصول على المعاملات
     */
    public function getTransactions(Request $request)
    {
        $filters = $request->only(['type', 'from_date', 'to_date', 'per_page']);
        $transactions = $this->walletService->getTransactions($request->user(), $filters);

        return $this->success(
            WalletTransactionResource::collection($transactions),
            'Wallet transactions retrieved successfully'
        );
    }
}
