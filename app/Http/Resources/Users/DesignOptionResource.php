<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $name = $this->getTranslations('name');
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => [
                'ar' => $name['ar'] ?? '',
                'en' => $name['en'] ?? '',
            ],
            'hex_color' => $this->when($this->type === 'color', $this->hex_color),
            'color_preview' => $this->when($this->type === 'color' && $this->hex_color, [
                'hex' => $this->hex_color,
                'rgb' => $this->hexToRgb($this->hex_color),
            ]),
        ];
    }

    /**
     * Convert hex color to RGB array
     */
    private function hexToRgb(?string $hex): ?array
    {
        if (!$hex) return null;

        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6) return null;

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }
}
