<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class InvoiceResource extends JsonResource
{
    protected $downloadUrl;
    public function __construct($resource, $downloadUrl)
    {
        parent::__construct($resource);
        $this->downloadUrl = $downloadUrl;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'order_id' => $this->order_id,
            'total' => $this->total,
            'download_url' => $this->downloadUrl,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'order' => $this->when($this->relationLoaded('order'), [
                'id' => $this->order?->id,
                'status' => $this->order?->status,
            ]),
        ];
    }
}
