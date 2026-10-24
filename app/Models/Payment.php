<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'status'])]
class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    public function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            // 'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }
}
