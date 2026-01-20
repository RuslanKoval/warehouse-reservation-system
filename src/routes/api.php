<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;

Route::post('order', [OrderController::class, 'store']);
Route::get('orders/{id}', [OrderController::class, 'show']);
Route::get('inventory/{sku}/movements', [InventoryController::class, 'movements']);
