<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;

/**
 * @group Monedas
 *
 * APIs para gestionar monedas y tasas de cambio
 */
class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Listar monedas
     *
     * @queryParam search string Buscar por nombre o símbolo. Example: dólar
     * @queryParam per_page int Resultados por página. Default: 15. Example: 10
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Dólar Estadounidense",
     *       "symbol": "USD",
     *       "exchange_rate": 1.0
     *     },
     *     {
     *       "id": 2,
     *       "name": "Euro",
     *       "symbol": "EUR",
     *       "exchange_rate": 0.92
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 10
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $currencies = $this->currencyService->list($request->all());
        return response()->json($currencies);
    }

    /**
     * Crear nueva moneda
     *
     * @bodyParam name string required Nombre completo de la moneda. Max: 255 chars. Example: Dólar Estadounidense
     * @bodyParam symbol string required Símbolo de la moneda. Example: USD
     * @bodyParam exchange_rate numeric required Tasa de cambio vs moneda base. Min: 0. Example: 1.0
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 3,
     *     "name": "Peso Mexicano",
     *     "symbol": "MXN",
     *     "exchange_rate": 20.5
     *   }
     * }
     * @response 500 {
     *   "success": false,
     *   "message": "Error al crear moneda"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string'],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $this->currencyService->create($data);
        return response()->json($result, $result['success'] ? 201 : 500);
    }

    /**
     * Mostrar moneda específica
     *
     * @urlParam id integer required ID de la moneda. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Dólar Estadounidense",
     *     "symbol": "USD",
     *     "exchange_rate": 1.0
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Moneda no encontrada"
     * }
     */
    public function show($id): JsonResponse
    {
        $result = $this->currencyService->find($id);
        if ($result['success']) {
            return response()->json($result, 200);
        }
        return response()->json($result, 404);
    }

    /**
     * Actualizar moneda
     *
     * Actualiza **al menos uno** de los campos de la moneda.
     *
     * @urlParam currency Modelo Currency required
     *
     * @bodyParam name string Campo opcional. Nombre de la moneda. Max: 255 chars. Example: Dólar Americano
     * @bodyParam symbol string Campo opcional. Símbolo de la moneda. Example: US$
     * @bodyParam exchange_rate numeric Campo opcional. Tasa de cambio vs moneda base. Min: 0. Example: 1.05
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Dólar Americano",
     *     "symbol": "US$",
     *     "exchange_rate": 1.05
     *   }
     * }
     * @response 422 {
     *   "message": "Debe enviar al menos un campo para actualizar (name, symbol o exchange_rate)"
     * }
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Validar que al menos un campo editable esté presente
        $updateableFields = ['name', 'symbol', 'exchange_rate'];
        $hasUpdateableData = array_intersect(array_keys($request->all()), $updateableFields);

        if (empty($hasUpdateableData)) {
            return response()->json([
                'message' => 'Debe enviar al menos un campo: name, symbol o exchange_rate'
            ], 422);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'symbol' => ['sometimes', 'string'],
            'exchange_rate' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $currency = $this->currencyService->update($id, $data);
        return response()->json($currency);
    }

    /**
     * Eliminar moneda
     *
     * @urlParam id integer required ID de la moneda. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Moneda eliminada exitosamente"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Moneda no encontrada"
     * }
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->currencyService->delete($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }
}
