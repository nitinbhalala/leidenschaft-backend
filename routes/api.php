<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\ProductInventoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ErrorLogController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\WishlistController;
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

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Contacts
    Route::apiResource('contacts', ContactController::class);

    // Settings
    Route::apiResource('settings', SettingController::class);

    // Products
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

    // Product Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('/', [ProductInventoryController::class, 'index']);
        Route::get('stats', [ProductInventoryController::class, 'stats']);
        Route::post('{id}/increase-stock', [ProductInventoryController::class, 'increaseStock']);
        Route::post('{id}/decrease-stock', [ProductInventoryController::class, 'decreaseStock']);
        Route::post('{id}/update-stock', [ProductInventoryController::class, 'updateStock']);
        Route::post('{id}/toggle-active', [ProductInventoryController::class, 'toggleActive']);
    });

    // Customers
    Route::apiResource('customers', CustomerController::class);

    // Customer Addresses
    Route::apiResource('customer-address', CustomerAddressController::class);

    // Cart
    Route::post('/add-to-cart', [CartController::class, 'addToCart']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Wishlist                                                                                                                                               
    Route::post('/add-to-wishlist', [WishlistController::class, 'addToWishlist']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // Policies
    Route::apiResource('policies', PolicyController::class);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Payment
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/create-order', [PaymentController::class, 'createOrder']);
        Route::post('/verify', [PaymentController::class, 'verify']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);
        Route::post('/{id}/refund', [PaymentController::class, 'refund']);
    });
});
