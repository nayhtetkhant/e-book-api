<?php

use App\Http\Controllers\User\V1\AuthController;
use App\Http\Controllers\User\V1\OrderController;
use App\Http\Controllers\User\V1\ProductController;
use App\Http\Controllers\User\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user/v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->prefix('user/v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    
    Route::get('/ordered' , [ProductController::class, 'getOrderedProducts']);
});