<?php

namespace App\Http\Services;

use App\Models\payment;

class PaymentService {


    public function cratePayment(array $data) { 
        return payment::create($data);
    }
}
