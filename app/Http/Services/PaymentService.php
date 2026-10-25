<?php

namespace App\Http\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService {


    public function createPayment(int $amount, array $data) { 
        $payment =  Payment::create($data);

        /* Model;user */
        $user = User::find(Auth::id());

        $checkout = $user->checkoutCharge($amount * 100, 'e-book-' . $payment->id, 1, [
            'success_url' => 'http://localhost:3000/user/dashboard/download?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:3000/user/dashboard',
            'metadata' => [
                'payment_id' => $payment->id,
            ],
        ]);

        return $checkout->url;
    }

    public function makeAsPaid(int $paymentId) {
        $payment = Payment::with('order.products')->find($paymentId);
        
        if (!$payment || !$payment->order) {
            return;
        }

        $productIds = $payment->order->products->pluck('id')->toArray();
        $user = $payment->order->user;

        DB::transaction(function () use ($payment, $user, $productIds) { 
            $payment->update([
                'status' => PaymentStatus::SUCCESS
            ]);

            $payment->order->update([
                'status' => 'paid'
            ]);

            if ($user) {
                $user->products()->syncWithoutDetaching($productIds);
            }
        });
    }
}
 