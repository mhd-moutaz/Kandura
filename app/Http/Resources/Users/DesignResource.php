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
        return [
            'id' => $this->id,
            'name' => [
                'name_ar' => $this->name['ar'],
                'name_en' => $this->name['en'],
            ],
            'description' => [
                'description_ar' => $this->description['ar'],
                'description_en' => $this->description['en'],
            ],
            'price' => $this->price,
            'images' => $this->designImages,
            'measurements' => $this->measurements,
            'design_options' => $this->designOptions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
