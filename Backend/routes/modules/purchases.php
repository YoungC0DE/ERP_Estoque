<?php
use App\Http\Controllers\V1\PurchaseController;

Route::prefix('compras')->group(function () {
    Route::get('/', [PurchaseController::class, 'index']);
    Route::post('/', [PurchaseController::class, 'store']);
});