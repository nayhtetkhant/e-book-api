<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PhpParser\Node\Expr\FuncCall;

#[Fillable(['user_id', 'total_amount'])]
class Order extends Model
{
    public function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class);
    }
}
