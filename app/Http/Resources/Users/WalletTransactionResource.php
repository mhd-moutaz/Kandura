<?php
namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'formatted_amount' => '$' . number_format($this->amount, 2),
            'balance_before' => (float) $this->balance_before,
            'balance_after' => (float) $this->balance_after,
            'description' => $this->description,
            'stripe_payment_intent_id' => $this->stripe_payment_intent_id,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
