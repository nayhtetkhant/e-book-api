<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PhpParser\Node\Expr\FuncCall;

#[Fillable(['user_id', 'total_amount' , 'stripe_session_id'])]
class Order extends Model
{
    public function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    public function products(): BelongsToMany 
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot(['quantity', 'amount'])
                    ->withTimestamps();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
