<?php

namespace App\Http\Resources\Admin\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->resource->id,
            "status"=> $this->resource->status,
            "totalAmount" => $this->whenLoaded("order" , fn () => $this->resource->order->total_amount),
        ];
    }
}
