<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Services\Product\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->productService->getAll();

        return ProductResource::collection($products);
    }

    public function all(Request $request) { 
        return $this->productService->all($request);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Product|JsonResponse
    {
        $product = $this->productService->getById($id);

        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id): ProductResource|JsonResponse
    {
        $product = $this->productService->update($request, $id);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->productService->delete($id);
    }
}
