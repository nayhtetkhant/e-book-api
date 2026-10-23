<?php

namespace App\Http\Services;

use App\Models\payment;

class PaymentService {


    public function createPayment(array $data) { 
        return payment::create($data);
    }
}
