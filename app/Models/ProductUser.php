<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'product_id'])]
class ProductUser extends Model
{
    /** @use HasFactory<\Database\Factories\ProductUserFactory> */
    use HasFactory;
}
