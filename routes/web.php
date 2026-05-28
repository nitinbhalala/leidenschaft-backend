<?php

use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\RetryPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Signed link from payment reminder emails → creates new Razorpay Payment Link → redirects
Route::get('/payment/retry', [RetryPaymentController::class, 'redirect'])
    ->name('payment.retry')
    ->middleware('signed');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::post('/products/{id}/fetch-images', [ProductImageController::class, 'fetchImages']);
    Route::post('/products/fetch-all-images', [ProductImageController::class, 'fetchAllImages']);
});
