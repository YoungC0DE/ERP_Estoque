<?php
use App\Http\Controllers\V1\SaleController;

Route::prefix('vendas')->group(function () {
    Route::get('/', [SaleController::class, 'index']);
    Route::post('/', [SaleController::class, 'store']);
});