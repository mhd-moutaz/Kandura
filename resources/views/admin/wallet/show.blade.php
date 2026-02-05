{{-- resources/views/admin/wallet/show.blade.php --}}
@extends('layouts.admin')

@section('title', __('messages.manage_wallet') . ' - ' . $user->name)

@section('content')

<div style="max-width:1200px;margin:0 auto;">

    <!-- Header -->
    <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('users.index') }}"
               style="display:flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;padding:8px 14px;background:white;border-radius:6px;border:1px solid #e2e8f0;transition:all 0.2s;font-size:14px;">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('messages.back_to_users') }}</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #10b981;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #dc2626;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- User & Wallet Info Card -->
    <div style="background:white;border-radius:10px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">

            <!-- User Avatar -->
            <div>
                @if ($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                        alt="{{ $user->name }}"
                        style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #3b82f6;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                @else
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:32px;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- User Info -->
            <div style="flex:1;">
                <h2 style="font-size:24px;font-weight:600;color:#1f2937;margin:0 0 4px 0;">
                    {{ $user->name }}
                </h2>
                <p style="font-size:14px;color:#6b7280;margin:0 0 8px 0;">
                    {{ $user->email }}
                </p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <span style="background:#edf2f7;color:#4a5568;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                        ID: #{{ $user->id }}
                    </span>
                    <span style="background:{{ $user->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $user->is_active ? '#065f46' : '#991b1b' }};padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                        {{ $user->is_active ? __('messages.active') : __('messages.inactive') }}
                    </span>
                </div>
            </div>

            <!-- Current Balance -->
            <div style="text-align:center;padding:20px 30px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:12px;">
                <div style="font-size:14px;color:rgba(255,255,255,0.9);margin-bottom:4px;">{{ __('messages.current_balance') }}</div>
                <div style="font-size:36px;font-weight:700;color:white;">
                    ${{ number_format($wallet->balance, 2) }}
                </div>
            </div>

        </div>
    </div>

    <!-- Actions Card -->
    <div style="background:white;border-radius:10px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;margin-bottom:24px;">
        <h3 style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-cog" style="color:#3b82f6;"></i>
            {{ __('messages.wallet_actions') }}
        </h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

            <!-- Deposit Form -->
            <div style="background:#f0fdf4;padding:20px;border-radius:10px;border:2px solid #86efac;">
                <h4 style="font-size:16px;font-weight:600;color:#065f46;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('messages.add_balance') }}
                </h4>
                <form action="{{ route('admin.wallet.deposit', $user) }}" method="POST">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:13px;color:#065f46;margin-bottom:6px;font-weight:500;">{{ __('messages.amount_usd') }}</label>
                        <input type="number"
                               name="amount"
                               min="0.01"
                               max="100000"
                               step="0.01"
                               required
                               placeholder="{{ __('messages.enter_amount') }}"
                               style="width:100%;padding:10px;border:2px solid #86efac;border-radius:6px;font-size:14px;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;color:#065f46;margin-bottom:6px;font-weight:500;">{{ __('messages.description_optional') }}</label>
                        <textarea name="description"
                                  rows="2"
                                  placeholder="{{ __('messages.reason_for_deposit') }}"
                                  style="width:100%;padding:10px;border:2px solid #86efac;border-radius:6px;font-size:14px;resize:vertical;"></textarea>
                    </div>
                    <button type="submit"
                            style="width:100%;padding:12px;background:#10b981;color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s;">
                        <i class="fas fa-check"></i> {{ __('messages.add_balance') }}
                    </button>
                </form>
            </div>

            <!-- Withdraw Form -->
            <div style="background:#fef2f2;padding:20px;border-radius:10px;border:2px solid #fca5a5;">
                <h4 style="font-size:16px;font-weight:600;color:#991b1b;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-minus-circle"></i>
                    {{ __('messages.deduct_balance') }}
                </h4>
                <form action="{{ route('admin.wallet.withdraw', $user) }}" method="POST"
                      onsubmit="return confirm('{{ __('messages.deduct_balance_confirm') }}');">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:13px;color:#991b1b;margin-bottom:6px;font-weight:500;">{{ __('messages.amount_usd') }}</label>
                        <input type="number"
                               name="amount"
                               min="0.01"
                               max="{{ $wallet->balance }}"
                               step="0.01"
                               required
                               placeholder="{{ __('messages.enter_amount') }}"
                               style="width:100%;padding:10px;border:2px solid #fca5a5;border-radius:6px;font-size:14px;">
                        <div style="font-size:11px;color:#991b1b;margin-top:4px;">
                            {{ __('messages.max') }}: ${{ number_format($wallet->balance, 2) }}
                        </div>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;color:#991b1b;margin-bottom:6px;font-weight:500;">{{ __('messages.description_optional') }}</label>
                        <textarea name="description"
                                  rows="2"
                                  placeholder="{{ __('messages.reason_for_deduction') }}"
                                  style="width:100%;padding:10px;border:2px solid #fca5a5;border-radius:6px;font-size:14px;resize:vertical;"></textarea>
                    </div>
                    <button type="submit"
                            style="width:100%;padding:12px;background:#dc2626;color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s;">
                        <i class="fas fa-times"></i> {{ __('messages.deduct_balance') }}
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Transactions History -->
    <div style="background:white;border-radius:10px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
        <h3 style="font-size:18px;font-weight:600;color:#1f2937;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-history" style="color:#3b82f6;"></i>
            {{ __('messages.transaction_history') }}
        </h3>

        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb;">
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">ID</th>
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.type') }}</th>
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.amount') }}</th>
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.balance_before') }}</th>
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.balance_after') }}</th>
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.description') }}</th>
                    <th style="padding:12px;text-align:left;font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:12px;font-size:13px;color:#4a5568;">#{{ $transaction->id }}</td>
                        <td style="padding:12px;">
                            @php
                                $typeColors = [
                                    'deposit' => ['bg' => '#d1fae5', 'text' => '#065f46', 'icon' => 'arrow-up'],
                                    'withdraw' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'arrow-down'],
                                    'payment' => ['bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'shopping-cart'],
                                    'refund' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'icon' => 'undo'],
                                ];
                                $color = $typeColors[$transaction->type] ?? ['bg' => '#f3f4f6', 'text' => '#4b5563', 'icon' => 'question'];
                            @endphp
                            <span style="background:{{ $color['bg'] }};color:{{ $color['text'] }};padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                                <i class="fas fa-{{ $color['icon'] }}"></i> {{ __('messages.' . $transaction->type) }}
                            </span>
                        </td>
                        <td style="padding:12px;font-size:14px;font-weight:600;color:{{ in_array($transaction->type, ['deposit', 'refund']) ? '#10b981' : '#dc2626' }};">
                            {{ in_array($transaction->type, ['deposit', 'refund']) ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                        </td>
                        <td style="padding:12px;font-size:13px;color:#6b7280;">${{ number_format($transaction->balance_before, 2) }}</td>
                        <td style="padding:12px;font-size:13px;color:#1f2937;font-weight:600;">${{ number_format($transaction->balance_after, 2) }}</td>
                        <td style="padding:12px;font-size:13px;color:#4a5568;">{{ $transaction->description ?? __('messages.n_a') }}</td>
                        <td style="padding:12px;font-size:13px;color:#6b7280;">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                            <i class="fas fa-inbox" style="font-size:48px;margin-bottom:10px;display:block;"></i>
                            {{ __('messages.no_transactions_yet') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($transactions->hasPages())
            <div style="margin-top:20px;">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
