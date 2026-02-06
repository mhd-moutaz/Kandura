<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $name = $this->getTranslations('name');
        $description = $this->getTranslations('description');
        return [
            'id' => $this->id,
            'name' => [
                'name_ar' => $name['ar'] ?? '',
                'name_en' => $name['en'] ?? '',
            ],
            'description' => [
                'description_ar' => $description['ar'] ?? '',
                'description_en' => $description['en'] ?? '',
            ],
            'price' => $this->price,
            'quantity' => $this->quantity,
            'in_stock' => $this->quantity > 0,
            'images' => $this->designImages,
            'measurements' => $this->measurements,
            'design_options' => DesignOptionResource::collection($this->designOptions),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
