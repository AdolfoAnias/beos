<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->list($request->all());

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['string'],
            'currency_id' => ['integer'],
            'tax_cost' => ['numeric', 'min:0'],
            'manufacturing_cost'=> ['numeric', 'min:0'],
        ]);

        $result = $this->productService->create($data);

        return response()->json(
            $result,
            $result['success'] ? 201 : 500
        );
    }

    public function show($id): JsonResponse
    {
        $result = $this->productService->find($id);

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json($result, 404);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'price'       => ['sometimes', 'required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $product = $this->productService->update($product, $data);

        return response()->json($product);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->productService->delete($id);

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function getProductPrice($id): JsonResponse
    {
        $result = $this->productService->listProductPrices($id);

        return response()->json(
            $result,
            $result['success'] ? 200 : 404
        );
    }

    public function postProductPrice(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
        ]);

        $result = $this->productService->createProductPrice($id, $data);

        return response()->json(
            $result,
            $result['success'] ? 201 : 422
        );
    }
}
