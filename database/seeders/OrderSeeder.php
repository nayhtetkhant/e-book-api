<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $this->call([UserSeeder::class, ProductSeeder::class]);

        foreach ([
            ['reader@example.com', OrderStatus::COMPLETED, PaymentStatus::SUCCESS, ['Laravel in Practice' => 1, 'Modern React Patterns' => 1]],
            ['pending.reader@example.com', OrderStatus::PENDING, PaymentStatus::PENDING, ['TypeScript Essentials' => 2]],
            ['failed.reader@example.com', OrderStatus::FAILED, PaymentStatus::FAILED, ['Database Design Handbook' => 1]],
        ] as [$email, $orderStatus, $paymentStatus, $items]) {
            DB::transaction(function () use ($email, $orderStatus, $paymentStatus, $items): void {
                $user = User::where('email', $email)->firstOrFail();

                // Each demo reader owns one sample order; preserve it after checkout changes its status.
                if (Order::where('user_id', $user->id)->exists()) {
                    return;
                }

                $products = Product::whereIn('title', array_keys($items))->get();
                $pivotData = $products->mapWithKeys(fn (Product $product): array => [
                    $product->id => [
                        'quantity' => $items[$product->title],
                        'amount' => $product->price * $items[$product->title],
                    ],
                ])->all();

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => array_sum(array_column($pivotData, 'amount')),
                    'status' => $orderStatus,
                ]);
                $order->products()->attach($pivotData);

                Payment::create(['order_id' => $order->id, 'status' => $paymentStatus]);

                if ($paymentStatus === PaymentStatus::SUCCESS) {
                    $user->products()->syncWithoutDetaching($products->modelKeys());
                }
            });
        }
    }
}
