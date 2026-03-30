<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('login.rate-limit');
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        require('modules/products.php');
        require('modules/sales.php');
        require('modules/purchases.php');

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});