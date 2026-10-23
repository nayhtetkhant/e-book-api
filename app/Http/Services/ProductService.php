<?php

namespace App\Http\Services;

use App\Models\Product;

class ProductService {
    public function createProduct(array $data) {
        return Product::create($data);
    }

    public function getAllProducts() {
        return Product::all();
    }

    public function getProduct(Product $product) {
        return $product;
    }

    public function updateProduct(Product $product, array $data) {
        $product->update($data);
        return $product;
    }
}