<?php

namespace App\Http\Services;

use App\Models\payment;

class PaymentService {
    private function afterSuccessPayment(payment $payment) { 
        // after success payment func
    }


    public function cratePayment(array $data) { 
        return payment::create($data);
    }

    public function successPayment(payment $payment) {
        // success payment func
    }
}
