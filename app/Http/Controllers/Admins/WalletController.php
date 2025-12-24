<?php
// app/Http/Controllers/Admins/WalletController.php

namespace App\Http\Controllers\Admins;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Global\WalletService;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * عرض صفحة إدارة المحفظة للمستخدم
     */
    public function showUserWallet(User $user)
    {
        $wallet = $user->getOrCreateWallet();
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.wallet.show', compact('user', 'wallet', 'transactions'));
    }

    /**
     * إضافة رصيد لمستخدم (Admin فقط)
     */
    public function deposit(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:100000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $transaction = $this->walletService->deposit(
                $user,
                $request->amount,
                $request->description ?? 'Balance added by admin',
                [
                    'admin_id' => Auth::id(),
                    'admin_name' => Auth::user()->name,
                    'admin_email' => Auth::user()->email,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ]
            );

            return redirect()->back()->with(
                'success',
                'Successfully added ' . number_format($request->amount, 2) . ' to ' . $user->name . '\'s wallet. New balance: ' . number_format($transaction->balance_after, 2)
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * سحب رصيد من مستخدم (Admin فقط)
     */
    public function withdraw(Request $request, User $user)
    {
        $wallet = $user->getOrCreateWallet();

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $transaction = $this->walletService->withdraw(
                $user,
                $request->amount,
                $request->description ?? 'Balance deducted by admin',
                [
                    'admin_id' => Auth::id(),
                    'admin_name' => Auth::user()->name,
                    'admin_email' => Auth::user()->email,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ]
            );

            return redirect()->back()->with(
                'success',
                'Successfully deducted ' . number_format($request->amount, 2) . ' from ' . $user->name . '\'s wallet. New balance: ' . number_format($transaction->balance_after, 2)
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
