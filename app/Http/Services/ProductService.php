<?php

namespace App\Http\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductService {
    public function createProduct(array $data) {
        if (Auth::user()->role === 'admin') {
            return Product::create($data);
        }
        throw new \Exception('You are not authorized to access this resource');
    }

    public function getAllProducts() {
        return Product::all();
    }

    public function getProduct(Product $product) {
        return $product;
    }

    public function updateProduct(Product $product, array $data) {
        if (Auth::user()->role === 'admin') {
            $product->update($data);
            return $product;
        }
        throw new \Exception('You are not authorized to access this resource');
    }
}