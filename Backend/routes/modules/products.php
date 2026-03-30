<?php
use App\Http\Controllers\V1\ProductController;

Route::prefix('produtos')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{productId}', [ProductController::class, 'show']);
    Route::put('/{productId}', [ProductController::class, 'update']);
    Route::delete('/{productId}', [ProductController::class, 'destroy']);
});