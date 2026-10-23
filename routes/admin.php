<?php

use App\Http\Controllers\Admin\V1\UserController;
use Illuminate\Routing\Route;

Route::middleware('auth:sanctum')->prefix('admin/v1')->group(function () {
    Route::apiResource('users', UserController::class);
});