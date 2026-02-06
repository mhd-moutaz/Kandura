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
        // Handle name (could be string or array/JSON)
        $name = $this->name ?? [];
        if (is_string($name)) {
            $name = json_decode($name, true) ?? [];
        }

        // Handle description (could be string or array/JSON)
        $description = $this->description ?? [];
        if (is_string($description)) {
            $description = json_decode($description, true) ?? [];
        }

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
