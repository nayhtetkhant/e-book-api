<?php

namespace App\Http\Controllers\User\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\V1\ProductStoreRequest;
use App\Http\Resources\User\V1\ProductResource;
use App\Http\Services\ProductService;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $data = [
            ...$request->validated(),
            'file_path' => $request->filePath,
        ];
        return response()->json(
            ProductResource::make($this->productService->createProduct($data)),
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
