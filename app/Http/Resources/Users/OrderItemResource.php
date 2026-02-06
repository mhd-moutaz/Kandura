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
                $name = $option->name ?? [];
                if (is_string($name)) {
                    $name = json_decode($name, true) ?? [];
                }

                $data = [
                    'id' => $option->id,
                    'type' => $option->type,
                    'name' => [
                        'ar' => $name['ar'] ?? '',
                        'en' => $name['en'] ?? '',
                    ],
                ];

                // Add color information for color type options
                if ($option->type === 'color' && $option->hex_color) {
                    $data['hex_color'] = $option->hex_color;
                    $data['color_preview'] = [
                        'hex' => $option->hex_color,
                    ];
                }

                return $data;
            }),
            'quantity' => $this->quantity,
            'unit_price' => number_format($this->unit_price, 2),
            'total_price' => number_format($this->total_price, 2),
            'created_at' => $this->created_at,
        ];
    }
}
