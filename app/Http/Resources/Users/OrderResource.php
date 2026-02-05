<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'address_id' => AddressResource::make($this->whenLoaded('address')),
            'status' => $this->status,
            'total_before_discount' => $this->total_before_discount ? number_format($this->total_before_discount, 2) : null,
            'discount_amount' => number_format($this->discount_amount ?? 0, 2),
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'coupon' => $this->when($this->coupon_id, function() {
                return [
                    'code' => $this->coupon->code,
                    'discount_type' => $this->coupon->discount_type,
                    'discount_value' => $this->coupon->discount_value,
                ];
            }),
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'note' => $this->note,
            'invoice_download_url' => $this->when($this->invoice_download_url, $this->invoice_download_url),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
