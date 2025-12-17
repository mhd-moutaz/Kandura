<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'design' => [
                'id' => $this->design->id,
                'name' => $this->design->name,
                'description' => $this->design->description,
            ],
            'measurement' => [
                'id' => $this->measurement->id,
                'size' => $this->measurement->size,
            ],
            'design_options' => $this->designOptions->map(function ($option) {
                return [
                    'id' => $option->id,
                    'type' => $option->type,
                    'name' => $option->name,
                ];
            }),
            'quantity' => $this->quantity,
            'unit_price' => number_format($this->unit_price, 2),
            'total_price' => number_format($this->total_price, 2),
            'created_at' => $this->created_at,
        ];
    }
}
