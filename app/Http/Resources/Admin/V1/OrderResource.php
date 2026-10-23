<?php

namespace App\Http\Resources\Admin\V1;

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
            'id' => $this->resource->id,
            'userId' => $this->resource->user_id,
            'totalAmount' => $this->resource->total_amount,
            'status' => $this->resource->status,
            'products' => $this->whenLoaded('products', function ($products) {
                return ProductResource::collection($products);
            }),
        ];
    }
}
