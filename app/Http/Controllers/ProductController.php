<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

/**
 * @group Gestión de Productos
 *
 * APIs para manejar productos y sus precios
 */
class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Listar productos
     *
     * @queryParam per_page int Tamaño de página. Default: 15
     * @queryParam search string Buscar por nombre. Example: laptop
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Laptop Dell",
     *       "price": 1200.00,
     *       "description": "Laptop gaming"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 50
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->list($request->all());
        return response()->json($products);
    }

    /**
     * Crear nuevo producto
     *
     * @bodyParam name string required Nombre del producto. Max: 255 chars. Example: Laptop Dell XPS
     * @bodyParam price numeric required Precio del producto. Min: 0. Example: 1299.99
     * @bodyParam description string Descripción opcional. Example: Laptop gaming de alta gama
     * @bodyParam currency_id integer ID de moneda. Example: 1
     * @bodyParam tax_cost numeric Costo de impuestos. Min: 0. Example: 150.50
     * @bodyParam manufacturing_cost numeric Costo de fabricación. Min: 0. Example: 800.00
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Dell XPS",
     *     "price": 1299.99
     *   }
     * }
     * @response 500 {
     *   "success": false,
     *   "message": "Error al crear producto"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'price'            => ['required', 'numeric', 'min:0'],
            'description'      => ['string'],
            'currency_id'      => ['integer'],
            'tax_cost'         => ['numeric', 'min:0'],
            'manufacturing_cost' => ['numeric', 'min:0'],
        ]);

        $result = $this->productService->create($data);
        return response()->json($result, $result['success'] ? 201 : 500);
    }

    /**
     * Mostrar producto específico
     *
     * @urlParam id integer required ID del producto. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Dell XPS",
     *     "price": 1299.99
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Producto no encontrado"
     * }
     */
    public function show($id): JsonResponse
    {
        $result = $this->productService->find($id);
        if ($result['success']) {
            return response()->json($result, 200);
        }
        return response()->json($result, 404);
    }

    /**
     * Actualizar producto
     *
     * @urlParam product Modelo Product required
     *
     * @bodyParam name string Campo opcional. Max: 255 chars.
     * @bodyParam price numeric Campo opcional. Min: 0.
     * @bodyParam description string Campo opcional.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Dell actualizada",
     *     "price": 1399.99
     *   }
     * }
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'price'       => ['sometimes', 'required', 'numeric', 'min:0'],
            'description' => ['sometimes', 'required', 'string'],
        ]);

        $product = $this->productService->update($id, $data);
        return response()->json($product);
    }

    /**
     * Eliminar producto
     *
     * @urlParam id integer required ID del producto. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Producto eliminado exitosamente"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Producto no encontrado"
     * }
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->productService->delete($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * @group Precios de Productos
     *
     * Obtener precios del producto
     *
     * @urlParam id integer required ID del producto. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "price": 1299.99,
     *       "currency_id": 1,
     *       "currency": "USD"
     *     }
     *   ]
     * }
     */
    public function getProductPrice($id): JsonResponse
    {
        $result = $this->productService->listProductPrices($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Crear precio para producto
     *
     * @urlParam id integer required ID del producto. Example: 1
     *
     * @bodyParam price numeric required Precio. Min: 0. Example: 1299.99
     * @bodyParam currency_id integer required ID de moneda. Example: 1
     * @bodyParam product_id integer required ID del producto. Example: 1
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "price": 1299.99,
     *     "currency_id": 1
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Datos inválidos"
     * }
     */
    public function postProductPrice(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'price'        => ['required', 'numeric', 'min:0'],
            'currency_id'  => ['required', 'integer'],
            'product_id'   => ['required', 'integer'],
        ]);

        $result = $this->productService->createProductPrice($id, $data);
        return response()->json($result, $result['success'] ? 201 : 422);
    }
}
