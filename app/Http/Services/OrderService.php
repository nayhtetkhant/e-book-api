<?php

namespace App\Http\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderService {
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    public function createOrder(array $data, array $products) {
        $totalAmount = 0;
        $pivotData = [];

        $productIds = array_column($products, 'id');
        $dbProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($products as $product) {
            $productId = $product['id'];
            $quantity = $product['quantity'];

            $price = $dbProducts[$productId]->price ?? 0;
            $amount = $price * $quantity;

            $totalAmount += $amount;

            $pivotData[$productId] = [
                'quantity' => $quantity,
                'amount'   => $amount,
            ];
        }

        $order = Order::create([
            ...$data,
            'user_id'      => Auth::id(),
            'total_amount' => $totalAmount,
        ]);

        $order->products()->attach($pivotData);

        $this->paymentService->createPayment([
            'order_id' => $order->id,
            'status'   => PaymentStatus::PENDING,
        ]);

        return $order;
    }
}