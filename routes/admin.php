<?php

use App\Http\Controllers\Admin\V1\UserController;
use App\Http\Controllers\Admin\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('admin/v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('products', ProductController::class);
});