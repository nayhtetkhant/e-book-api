<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\V1\ProductStoreRequest;
use App\Http\Requests\Admin\V1\ProductUpdateRequest;
use App\Http\Requests\User\V1\UserStoreRequest;
use App\Http\Resources\Admin\V1\ProductResource;
use App\Http\Services\ProductService;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct(
        public ProductService $productService,
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            ProductResource::collection($this->productService->getAllProducts())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request) {
        $product = $this->productService->createProduct($request->validated());
        
        $uploadUrl = null;
        if ($request->has('fileType')) {
            $uploadUrl = $this->productService->createUploadUrl($request->input('fileType'), $product);
        }

        return response()->json([
            "product" => ProductResource::make($product),
            "uploadUrl" => $uploadUrl
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json(
            ProductResource::make($this->productService->getProduct($product))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $data = [
            ...$request->validated(),
            'file_path' => $request->validated('filePath')
        ];
        return response()->json(
            ProductResource::make($this->productService->updateProduct($product, $data))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
