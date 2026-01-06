<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query();

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function find($id): array
    {
        try {
            $product = Product::findOrFail($id);

            return [
                'success' => true,
                'message' => 'Producto encontrado',
                'data' => $product
            ];

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error en el servidor'
            ];
        }
    }

    public function create(array $data): array
    {
        try {
            $product = Product::create($data);

            return [
                'success' => true,
                'message' => 'Producto creado correctamente',
                'data'    => $product
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear el producto',
            ];
        }
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete($id): array
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ];
            }

            $deleted = $product->delete();

            return [
                'success' => $deleted,
                'message' => $deleted ? 'Producto eliminado correctamente' : 'Error al eliminar el producto'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ];
        }
    }

    public function createProductPrice($productId, array $data): array
    {
       try {
            $product = Product::findOrFail($productId);

            $price = $product->prices()->create([
                'price'             => $data['price'],
                'currency_id'       => $data['currency_id'] ?? $product->currency_id,
                'product_id'       => $data['product_id'] ?? $product->product_id,
            ]);

            return [
                'success' => true,
                'message' => 'Precio creado correctamente para el producto',
                'data'    => $price->load('product', 'currency')
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear el precio del producto'
            ];
        }
    }

    public function listProductPrices($productId): array
    {
        try {
            $product = Product::find($productId);

            $prices = $product->prices()
                ->with(['currency'])
                ->get();

            return [
                'success' => true,
                'message' => 'Lista de precios obtenida correctamente',
                'data'    => $prices
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener los precios del producto'
            ];
        }
    }
}
