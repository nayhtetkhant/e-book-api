<?php

use App\Http\Services\ProductService;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

test('product service supports pdf content types', function () {
    expect(ProductService::FILE_EXTENSIONS)->toHaveKey('application/pdf')
        ->and(ProductService::FILE_EXTENSIONS['application/pdf'])->toBe('pdf')
        ->and(ProductService::IMAGE_EXTENSIONS)->toHaveKey('application/pdf')
        ->and(ProductService::IMAGE_EXTENSIONS['application/pdf'])->toBe('pdf');
});

test('createUploadUrl generates a pdf file path when application/pdf is passed', function () {
    Storage::fake('s3');

    $product = Mockery::mock(Product::class);
    $product->shouldReceive('update')
        ->once()
        ->withArgs(function ($data) {
            return str_ends_with($data['file_path'], '.pdf') && str_starts_with($data['file_path'], 'products/');
        });

    $service = new ProductService();
    $result = $service->createUploadUrl('application/pdf', $product);

    expect($result['file_path'])->toEndWith('.pdf')
        ->and($result['headers']['Content-Type'])->toBe('application/pdf');
});

test('createUploadUrl throws exception for unsupported content type', function () {
    $product = Mockery::mock(Product::class);
    $service = new ProductService();

    $service->createUploadUrl('application/zip', $product);
})->throws(\Exception::class, 'Unsupported content type: application/zip');
