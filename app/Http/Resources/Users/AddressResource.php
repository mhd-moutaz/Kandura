<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Handle city name (could be string or array/JSON)
        $cityName = $this->city->name ?? [];
        if (is_string($cityName)) {
            $cityName = json_decode($cityName, true) ?? [];
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'city' => [
                'id'        => $this->city->id,
                'name_en' => $cityName['en'] ?? '',
                'name_ar' => $cityName['ar'] ?? '',
            ],
            'district' => $this->district,
            'street' => $this->street,
            'house_number' => $this->house_number,
            'notes' => $this->notes,
            'Langitude' => $this->Langitude,
            'Latitude' => $this->Latitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
