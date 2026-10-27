<?php

namespace App\Http\Services;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Str;

class ProductService {
    public const IMAGE_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public function createProduct(array $data) {
        if (Auth::user()->role === UserRole::ADMIN) {
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
        if (Auth::user()->role === UserRole::ADMIN) {
            $product->update($data);
            return $product;
        }
        throw new \Exception('You are not authorized to access this resource');
    }

    public function getOrderProducts() {
        return User::find(Auth::user()->id)->products;
    }

    public function createUploadUrl(string $contentType , Product $product) {
        $filePath = 'products/'.\Illuminate\Support\Str::uuid().'.'.self::IMAGE_EXTENSIONS[$contentType];
        $expiresAt = now()->addMinutes(10);

        $upload = Storage::disk('s3')->temporaryUploadUrl($filePath, $expiresAt);

        $product->update([ 
            'file_path' => $filePath
        ]);

        return [
            'file_path' => $filePath,
            'url' => $upload,
            'headers' => ['Content-Type' => $contentType],
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    public function createDownloadUrl(){
        
    }
}