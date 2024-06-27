<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\SettingController;

Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('products', ProductController::class)->middleware(['auth:sanctum']);
Route::apiResource('orders', OrderController::class)->middleware(['auth:sanctum']);
Route::apiResource('payment-methods', PaymentMethodController::class)->middleware(['auth:sanctum']);
Route::get('products/barcode/{barcode}', [ProductController::class, 'showByBarcode'])->middleware(['auth:sanctum']);
Route::get('setting', [SettingController::class, 'index'])->middleware(['auth:sanctum']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
