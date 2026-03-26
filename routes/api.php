<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\ProductInventoryController;
use App\Http\Controllers\Api\Admin\ProfileController;
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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Social Auth
    Route::prefix('auth/{provider}')->where(['provider' => 'google|facebook'])->group(function () {
        Route::get('/', [SocialAuthController::class, 'redirect'])->name('social.redirect');
        Route::get('/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');

    Route::middleware('admin.token')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/verify-token', [AdminAuthController::class, 'verifyToken']);
        Route::post('/refresh-token', [AdminAuthController::class, 'refreshToken']);

        // Admin profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::post('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('/sub-categories/{id?}', [CategoryController::class, 'subCategories']);
            Route::get('/{category}', [CategoryController::class, 'show']);
            Route::post('/{category}', [CategoryController::class, 'update']);
            Route::delete('/{category}', [CategoryController::class, 'destroy']);
            Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus']);
        });

        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/{product}', [ProductController::class, 'show']);
            Route::post('/{product}', [ProductController::class, 'update']);
            Route::delete('/{product}', [ProductController::class, 'destroy']);
            Route::get('/{product_id}/reviews', [ProductReviewController::class, 'index']);
            Route::post('/{id}/toggle-status', [ProductController::class, 'toggleActive']);
        });

        // Product Reviews
        Route::delete('product-reviews/{productReview}', [ProductReviewController::class, 'destroy']);

        // Product Inventory
        Route::prefix('inventory')->group(function () {
            Route::get('/', [ProductInventoryController::class, 'index']);
            Route::get('/stats', [ProductInventoryController::class, 'stats']);
            Route::get('/{id}', [ProductInventoryController::class, 'show']);
            Route::post('/update-stock', [ProductInventoryController::class, 'updateStockUniversal']);
            Route::post('/{id}/toggle-status', [ProductInventoryController::class, 'toggleActive']);
        });

        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{order}', [OrderController::class, 'show']);
            Route::put('/{order}', [OrderController::class, 'update']);
            Route::delete('/{order}', [OrderController::class, 'destroy']);
        });

        // Customers
        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::post('/', [CustomerController::class, 'store']);
            Route::get('/{customer}', [CustomerController::class, 'show']);
            Route::put('/{customer}', [CustomerController::class, 'update']);
            Route::delete('/{customer}', [CustomerController::class, 'destroy']);
        });

        // Customer Addresses
        Route::prefix('customer-address')->group(function () {
            Route::get('/', [CustomerAddressController::class, 'index']);
            Route::post('/', [CustomerAddressController::class, 'store']);
            Route::get('/{customer_address}', [CustomerAddressController::class, 'show']);
            Route::put('/{customer_address}', [CustomerAddressController::class, 'update']);
            Route::delete('/{customer_address}', [CustomerAddressController::class, 'destroy']);
        });

        // Payments
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index']);
            Route::get('/{id}', [PaymentController::class, 'show']);
            Route::delete('/{id}', [PaymentController::class, 'destroy']);
            Route::post('/{id}/refund', [PaymentController::class, 'refund']);
        });

        // Contacts
        Route::prefix('contacts')->group(function () {
            Route::get('/', [ContactController::class, 'index']);
            Route::post('/', [ContactController::class, 'store']);
            Route::get('/stats', [ContactController::class, 'stats']);
            Route::get('/{contact}', [ContactController::class, 'show']);
            Route::put('/{contact}', [ContactController::class, 'update']);
            Route::delete('/{contact}', [ContactController::class, 'destroy']);
        });

        // Settings
        Route::prefix('settings')->group(function () {
            Route::post('/bulk', [SettingController::class, 'bulk']);
            Route::get('/key/{key}', [SettingController::class, 'getByKey']);
            Route::get('/', [SettingController::class, 'index']);
            Route::post('/', [SettingController::class, 'store']);
            Route::put('/{key}', [SettingController::class, 'update']);
            Route::delete('/{key}', [SettingController::class, 'destroy']);
        });

        // Blogs
        Route::prefix('blogs')->group(function () {
            Route::get('/', [BlogController::class, 'index']);
            Route::post('/', [BlogController::class, 'store']);
            Route::get('/{blog}', [BlogController::class, 'show']);
            Route::put('/{blog}', [BlogController::class, 'update']);
            Route::delete('/{blog}', [BlogController::class, 'destroy']);
        });

        // FAQs
        Route::prefix('faqs')->group(function () {
            Route::get('/', [FaqController::class, 'index']);
            Route::post('/', [FaqController::class, 'store']);
            Route::get('/{faq}', [FaqController::class, 'show']);
            Route::put('/{faq}', [FaqController::class, 'update']);
            Route::delete('/{faq}', [FaqController::class, 'destroy']);
        });

        // Policies
        Route::prefix('policies')->group(function () {
            Route::get('/', [PolicyController::class, 'index']);
            Route::post('/', [PolicyController::class, 'store']);
            Route::get('/{slug}', [PolicyController::class, 'show'])->where('slug', '[a-z\-]+');
            Route::put('/{slug}', [PolicyController::class, 'update'])->where('slug', '[a-z\-]+');
            Route::delete('/{slug}', [PolicyController::class, 'destroy'])->where('slug', '[a-z\-]+');
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/', [NotificationController::class, 'store']);
            Route::get('/{notification}', [NotificationController::class, 'show']);
            Route::put('/{notification}', [NotificationController::class, 'update']);
            Route::delete('/{notification}', [NotificationController::class, 'destroy']);
            Route::post('/read/{id?}', [NotificationController::class, 'read']);
        });

        // Error Logs
        Route::prefix('error-logs')->group(function () {
            Route::get('/', [ErrorLogController::class, 'index']);
            Route::get('/{id}', [ErrorLogController::class, 'show']);
            Route::post('/', [ErrorLogController::class, 'store']);
            Route::patch('/{id}/resolve', [ErrorLogController::class, 'markResolved']);
            Route::delete('/{id}', [ErrorLogController::class, 'destroy']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Authenticated only - Customer)
|--------------------------------------------------------------------------
*/
Route::middleware('customer.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Categories (Customer can only view categories)
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/sub-categories/{id?}', [CategoryController::class, 'subCategories']);
        Route::get('/{category}', [CategoryController::class, 'show']);
    });

    // Contacts
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index']);
        Route::post('/', [ContactController::class, 'store']);
        Route::get('/{contact}', [ContactController::class, 'show']);
        Route::put('/{contact}', [ContactController::class, 'update']);
        Route::delete('/{contact}', [ContactController::class, 'destroy']);
    });

    // Products (Customer can only view products)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::get('/{product_id}/reviews', [ProductReviewController::class, 'index']);
    });

    // Product Reviews
    Route::prefix('product-reviews')->group(function () {
        Route::post('/', [ProductReviewController::class, 'store']);
        Route::delete('/{productReview}', [ProductReviewController::class, 'destroy']);
    });

    // Error Logs
    Route::prefix('error-logs')->group(function () {
        Route::get('/', [ErrorLogController::class, 'index']);
        Route::get('/{id}', [ErrorLogController::class, 'show']);
        Route::post('/', [ErrorLogController::class, 'store']);
        Route::patch('/{id}/resolve', [ErrorLogController::class, 'markResolved']);
        Route::delete('/{id}', [ErrorLogController::class, 'destroy']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
        Route::put('/{notification}', [NotificationController::class, 'update']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::post('/read/{id?}', [NotificationController::class, 'read']);
    });

    // FAQs
    Route::prefix('faqs')->group(function () {
        Route::get('/', [FaqController::class, 'index']);
        Route::post('/', [FaqController::class, 'store']);
        Route::get('/{faq}', [FaqController::class, 'show']);
        Route::put('/{faq}', [FaqController::class, 'update']);
        Route::delete('/{faq}', [FaqController::class, 'destroy']);
    });

    // Blogs
    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index']);
        Route::post('/', [BlogController::class, 'store']);
        Route::get('/{blog}', [BlogController::class, 'show']);
        Route::put('/{blog}', [BlogController::class, 'update']);
        Route::delete('/{blog}', [BlogController::class, 'destroy']);
    });

    // Product Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('/', [ProductInventoryController::class, 'index']);
        Route::get('/stats', [ProductInventoryController::class, 'stats']);
        Route::get('/{id}', [ProductInventoryController::class, 'show']);
        Route::post('/{id}/increase-stock', [ProductInventoryController::class, 'increaseStock']);
        Route::post('/{id}/decrease-stock', [ProductInventoryController::class, 'decreaseStock']);
        Route::post('/{id}/update-stock', [ProductInventoryController::class, 'updateStock']);
        Route::post('/{id}/toggle-status', [ProductInventoryController::class, 'toggleActive']);
    });

    // Customers
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{customer}', [CustomerController::class, 'show']);
        Route::put('/{customer}', [CustomerController::class, 'update']);
        Route::delete('/{customer}', [CustomerController::class, 'destroy']);
    });

    // Customer Addresses
    Route::prefix('customer-address')->group(function () {
        Route::get('/', [CustomerAddressController::class, 'index']);
        Route::post('/', [CustomerAddressController::class, 'store']);
        Route::get('/{customer_address}', [CustomerAddressController::class, 'show']);
        Route::put('/{customer_address}', [CustomerAddressController::class, 'update']);
        Route::delete('/{customer_address}', [CustomerAddressController::class, 'destroy']);
    });

    // Cart
    Route::post('/add-to-cart', [CartController::class, 'addToCart']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Wishlist
    Route::post('/add-to-wishlist', [WishlistController::class, 'addToWishlist']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // Policies
    Route::prefix('policies')->group(function () {
        Route::get('/', [PolicyController::class, 'index']);
        Route::post('/', [PolicyController::class, 'store']);
        Route::get('/{policy}', [PolicyController::class, 'show']);
        Route::put('/{policy}', [PolicyController::class, 'update']);
        Route::delete('/{policy}', [PolicyController::class, 'destroy']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/create-order', [PaymentController::class, 'createOrder']);
        Route::post('/verify', [PaymentController::class, 'verify']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);
        Route::post('/{id}/refund', [PaymentController::class, 'refund']);
    });
});
