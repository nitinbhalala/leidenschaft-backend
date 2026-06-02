<?php

use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\RetryPaymentController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/b7f3d9a2-8c41-4f6e-b2d9-91fa7c3e5a84', function () {
    Artisan::call('reminders:payment');

    return response()->json([
        'message' => 'Payment reminder command executed successfully.'
    ]);
});
