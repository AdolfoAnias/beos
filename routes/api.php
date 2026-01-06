<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\CurrencyController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/products/{id}/prices', [ProductController::class, 'getProductPrice']);
    Route::post('/products/{id}/prices', [ProductController::class, 'postProductPrice']);

    Route::get('/currencies', [CurrencyController::class, 'index']);
    Route::post('/currencies', [CurrencyController::class, 'store']);
    Route::get('/currencies/{id}', [CurrencyController::class, 'show']);
    Route::put('/currencies/{id}', [CurrencyController::class, 'update']);
    Route::delete('/currencies/{id}', [CurrencyController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

