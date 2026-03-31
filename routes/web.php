<?php

use App\Http\Controllers\ProductImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::post('/products/{id}/fetch-images', [ProductImageController::class, 'fetchImages']);
    Route::post('/products/fetch-all-images', [ProductImageController::class, 'fetchAllImages']);
});
