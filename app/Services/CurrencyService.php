<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CurrencyService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Currency::query();

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function find($id): array
    {
        try {
            $currency = Currency::findOrFail($id);

            return [
                'success' => true,
                'message' => 'Moneda encontrada',
                'data' => $currency
            ];

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Moneda no encontrada'
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
            $currency = Currency::create($data);

            return [
                'success' => true,
                'message' => 'Moneda creada correctamente',
                'data'    => $currency
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear la moneda',
            ];
        }
    }

    public function update($id, array $data): Currency
    {
        $currency = Currency::findOrFail($id);  // ⭐ Buscar por ID
        $currency->update($data);                // ⭐ Actualizar y guardar
        return $currency;
    }

    public function delete($id): array
    {
        try {
            $currency = Currency::find($id);

            if (!$currency) {
                return [
                    'success' => false,
                    'message' => 'Moneda no encontrada'
                ];
            }

            $deleted = $currency->delete();

            return [
                'success' => $deleted,
                'message' => $deleted ? 'Moneda eliminada correctamente' : 'Error al eliminar la moneda'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ];
        }
    }
}
