<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ErrorLogController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// GET     /api/categories
// POST    /api/categories
// GET     /api/categories/{id}
// PUT     /api/categories/{id}
// DELETE  /api/categories/{id}

// GET     /api/contacts
// POST    /api/contacts
// GET     /api/contacts/{id}
// PUT     /api/contacts/{id}
// DELETE  /api/contacts/{id}

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Register
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    // Login
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Admin Login
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');

    // Social Auth — Redirect to provider
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
        ->name('social.redirect')
        ->where('provider', 'google|facebook');

    // Social Auth — Callback from provider
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->name('social.callback')
        ->where('provider', 'google|facebook');
});

Route::prefix('admin')->group(function () {

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::post('/change-password', [AdminAuthController::class, 'changePassword']);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Authenticated only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    //logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // API Resources
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('products', ProductController::class);

    // Product Reviews
    Route::get('products/{product_id}/reviews', [ProductReviewController::class, 'index']);
    Route::post('product-reviews', [ProductReviewController::class, 'store']);
    Route::delete('product-reviews/{productReview}', [ProductReviewController::class, 'destroy']);

    // Error Logs
    Route::prefix('error-logs')->group(function () {
        Route::get('/', [ErrorLogController::class, 'index']);
        Route::get('/{id}', [ErrorLogController::class, 'show']);
        Route::post('/', [ErrorLogController::class, 'store']);
        Route::patch('/{id}/resolve', [ErrorLogController::class, 'markResolved']);
        Route::delete('/{id}', [ErrorLogController::class, 'destroy']);
    });

    // Notifications
    Route::apiResource('notifications', NotificationController::class);
    Route::post('notifications/read/{id?}', [NotificationController::class, 'read']);

    // FAQs
    Route::apiResource('faqs', FaqController::class);

    // Blogs
    Route::apiResource('blogs', BlogController::class);
});
