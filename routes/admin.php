<?php

use App\Http\Controllers\Admin\V1\AuthController;
use App\Http\Controllers\Admin\V1\OrderController;
use App\Http\Controllers\Admin\V1\PaymentController;
use App\Http\Controllers\Admin\V1\ProductController;
use App\Http\Controllers\Admin\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->prefix('admin/v1')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('payments', PaymentController::class);
});
