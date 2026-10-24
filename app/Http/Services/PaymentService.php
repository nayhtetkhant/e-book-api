<?php

namespace App\Http\Services;

use App\Models\Payment;

class PaymentService {


    public function createPayment(array $data) { 
        return Payment::create($data);
    }
}
