<?php

namespace App\Http\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;

class OrderService {
    public function __construct(
        private PaymentService $paymentService,
    )
    {
        
    }

    public function createOrder(array $data , array $productIds) {
        $order = Order::create($data);
        $order->products()->sync($productIds);
        $this->paymentService->cratePayment([
            'order_id' => $order->id,
            'status' => PaymentStatus::PENDING->value,
        ]);
        return $order;
    }

    
}