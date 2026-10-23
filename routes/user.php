<?php

use App\Http\Controllers\User\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('user/v1')->group(function () {
    Route::apiResource('users', UserController::class);
});