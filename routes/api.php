<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\Admin\ProductImportController;
use App\Http\Controllers\Api\Admin\ProductInventoryController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\ErrorLogController;
use App\Http\Controllers\Api\EssenceController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\HomePageController;
use App\Http\Controllers\Api\InteriorController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OfferTemplateController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\SceneController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SupportChatController;
use App\Http\Controllers\Api\SupportController;
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
        Route::post('/admin-logout', [AdminAuthController::class, 'logout']);
        Route::get('/verify-token', [AdminAuthController::class, 'verifyToken']);
        Route::post('/refresh-token', [AdminAuthController::class, 'refreshToken']);

        // Admin profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::post('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);

        // Roles
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{id}', [RoleController::class, 'show']);
            Route::put('/{id}', [RoleController::class, 'update']);
            Route::delete('/{id}', [RoleController::class, 'destroy']);
            Route::post('/{id}/assign-permissions', [RoleController::class, 'assignPermissions']);
            Route::post('/{id}/revoke-permissions', [RoleController::class, 'revokePermissions']);
            Route::post('/{id}/sync-permissions', [RoleController::class, 'syncPermissions']);
        });

        // Permissions
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::get('/grouped', [PermissionController::class, 'grouped']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{id}', [PermissionController::class, 'show']);
            Route::put('/{id}', [PermissionController::class, 'update']);
            Route::delete('/{id}', [PermissionController::class, 'destroy']);
        });

        // Assign / revoke roles for users
        Route::prefix('user-roles')->group(function () {
            Route::get('/{userId}', [RoleController::class, 'userRoles']);
            Route::post('/assign', [RoleController::class, 'assignToUser']);
            Route::post('/revoke', [RoleController::class, 'revokeFromUser']);
            Route::post('/sync', [RoleController::class, 'syncUserRoles']);
        });

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('/sub-categories/{id?}', [CategoryController::class, 'subCategories']);
            Route::get('/{category}', [CategoryController::class, 'show']);
            Route::post('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
            Route::post('/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
        });

        // Offers
        Route::prefix('offers')->group(function () {
            Route::get('/', [OfferController::class, 'index']);
            Route::post('/', [OfferController::class, 'store']);
            Route::get('/{offer}', [OfferController::class, 'show']);
            Route::post('/{id}', [OfferController::class, 'update']);
            Route::delete('/{id}', [OfferController::class, 'destroy']);
            Route::post('/{id}/toggle-status', [OfferController::class, 'toggleStatus']);
        });

        // Offer Templates
        Route::prefix('offers-templates')->group(function () {
            Route::get('/', [OfferTemplateController::class, 'index']);
            Route::post('/', [OfferTemplateController::class, 'store']);
            Route::get('/{id}', [OfferTemplateController::class, 'show']);
            Route::post('/{id}', [OfferTemplateController::class, 'update']);
            Route::delete('/{id}', [OfferTemplateController::class, 'destroy']);
            Route::post('/{id}/toggle-status', [OfferTemplateController::class, 'toggleStatus']);
        });

        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/list', [ProductController::class, 'list']);
            Route::post('/import', [ProductImportController::class, 'import']);
            Route::get('/{id}/reviews', [ProductReviewController::class, 'index']);
            Route::post('/{id}/toggle-status', [ProductController::class, 'toggleActive']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::post('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });

        // Product Reviews
        Route::delete('reviews/{productReview}', [ProductReviewController::class, 'destroy']);
        Route::post('reviews/{id}/toggle-status', [ProductReviewController::class, 'toggleStatus']);

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
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::put('/{id}', [OrderController::class, 'update']);
            Route::delete('/{id}', [OrderController::class, 'destroy']);
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
            Route::post('/{customer_address}/set-default', [CustomerAddressController::class, 'setDefault']);
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

        // Home Page Settings
        Route::prefix('home-page')->group(function () {
            Route::post('/settings', [HomePageController::class, 'index']);
            Route::post('/update-settings', [HomePageController::class, 'update']);
            Route::post('/toggle-status/{section}', [HomePageController::class, 'toggleStatus']);
        });

        Route::prefix('essence')->group(function () {
            Route::post('/settings', [EssenceController::class, 'index']);
            Route::post('/update-settings', [EssenceController::class, 'update']);
            Route::post('/toggle-status/{section}', [EssenceController::class, 'toggleStatus']);
        });

        Route::prefix('interior')->group(function () {
            Route::post('/settings', [InteriorController::class, 'index']);
            Route::post('/update-settings', [InteriorController::class, 'update']);
            Route::post('/toggle-status/{section}', [InteriorController::class, 'toggleStatus']);
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

        // Email Templates
        Route::prefix('email-templates')->group(function () {
            Route::get('/', [EmailTemplateController::class, 'index']);
            Route::post('/', [EmailTemplateController::class, 'store']);
            Route::get('/{id}', [EmailTemplateController::class, 'show']);
            Route::post('/{id}', [EmailTemplateController::class, 'update']);
            Route::delete('/{id}', [EmailTemplateController::class, 'destroy']);
        });

        // Error Logs
        Route::prefix('error-logs')->group(function () {
            Route::get('/', [ErrorLogController::class, 'index']);
            Route::get('/{id}', [ErrorLogController::class, 'show']);
            Route::post('/', [ErrorLogController::class, 'store']);
            Route::post('/{id}/resolve', [ErrorLogController::class, 'markResolved']);
            Route::delete('/{id}', [ErrorLogController::class, 'destroy']);
        });

        // Support Tickets (Admin)
        Route::prefix('supports')->group(function () {
            Route::get('/', [SupportController::class, 'index']);
            Route::get('/{id}', [SupportController::class, 'show']);
            Route::post('/{id}/status', [SupportController::class, 'updateStatus']);
        });

        // Support Chat (Admin)
        Route::prefix('support-chats')->group(function () {
            Route::get('/{supportId}', [SupportChatController::class, 'indexAsAdmin']);
            Route::post('/{supportId}', [SupportChatController::class, 'sendAsAdmin']);
            Route::get('/{supportId}/unread-count', [SupportChatController::class, 'unreadCount']);
            Route::post('/{supportId}/mark-all-read', [SupportChatController::class, 'markAllAsRead']);
        });

        // Scenes
        Route::prefix('scenes')->group(function () {
            Route::get('/', [SceneController::class, 'index']);
            Route::post('/', [SceneController::class, 'store']);
            Route::get('/{scene}', [SceneController::class, 'show']);
            Route::post('/{scene}', [SceneController::class, 'update']);
            Route::delete('/{scene}', [SceneController::class, 'destroy']);
            Route::post('/{id}/toggle-status', [SceneController::class, 'toggleStatus']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Authenticated only - Customer)
|--------------------------------------------------------------------------
*/
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('customer.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);

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
        Route::get('/{customer_id}', [CustomerAddressController::class, 'index']);
        Route::post('/', [CustomerAddressController::class, 'store']);
        Route::get('/show/{id}', [CustomerAddressController::class, 'show']);
        Route::put('/{id}', [CustomerAddressController::class, 'update']);
        Route::delete('/{id}', [CustomerAddressController::class, 'destroy']);
        Route::post('/{id}/set-default', [CustomerAddressController::class, 'setDefault']);
    });

    // Cart
    Route::get('/cart/{customer_id}', [CartController::class, 'getCart']);
    Route::post('/add-to-cart', [CartController::class, 'addToCart']);
    Route::put('/cart/{id}', [CartController::class, 'updateCart']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Wishlist
    Route::post('/add-to-wishlist', [WishlistController::class, 'addToWishlist']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'customerOrders']);
        Route::get('/{id}', [OrderController::class, 'customerShow']);
        Route::get('/{id}/invoice', [OrderController::class, 'invoice']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);
        Route::post('/{id}/refund', [PaymentController::class, 'refund']);
    });

    // Support Tickets (Customer)
    Route::prefix('supports')->group(function () {
        Route::get('/', [SupportController::class, 'customerIndex']);
        Route::post('/', [SupportController::class, 'store']);
        Route::get('/{id}', [SupportController::class, 'show']);
        Route::post('/{id}/status', [SupportController::class, 'updateStatus']);
    });

    // Support Chat (Customer)
    Route::prefix('support-chats')->group(function () {
        Route::get('/{supportId}', [SupportChatController::class, 'indexAsCustomer']);
        Route::post('/{supportId}', [SupportChatController::class, 'sendAsCustomer']);
        Route::post('/{supportId}/mark-all-read', [SupportChatController::class, 'markAllAsRead']);
    });
});

// Checkout
Route::post('/checkout', [CheckoutController::class, 'processCheckout']);

Route::post('/contact/store', [ContactController::class, 'store']);

Route::get('/payment/callback', [PaymentController::class, 'callback']);

// Public, no-auth batch product importer — open in a browser, imports one
// chunk file from public/import-chunks/pending/ per call.
Route::get('/products-import/run-chunk', [ProductImportController::class, 'runChunk']);

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/sub-categories/{id?}', [CategoryController::class, 'subCategories']);
    Route::get('/{category}', [CategoryController::class, 'show']);
});

Route::prefix('offers')->group(function () {
    Route::get('/', [OfferController::class, 'index']);
    Route::get('/{offer}', [OfferController::class, 'show']);
});

Route::prefix('offer-templates')->group(function () {
    Route::get('/', [OfferTemplateController::class, 'index']);
    Route::get('/{id}', [OfferTemplateController::class, 'show']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/best-selling', [ProductController::class, 'bestSelling']);
    Route::get('/{product:slug}', [ProductController::class, 'customerShow']);
    Route::get('/{product_id}/reviews', [ProductReviewController::class, 'index']);
});

Route::prefix('product-reviews')->group(function () {
    Route::get('/{slug}', [ProductReviewController::class, 'reviewsBySlug']);
    Route::post('/create', [ProductReviewController::class, 'store']);
    Route::delete('/{productReview}', [ProductReviewController::class, 'destroy']);
});

Route::get('/home-page', [HomePageController::class, 'index']);

Route::get('/essence', [EssenceController::class, 'index']);

Route::get('/interior', [InteriorController::class, 'index']);

Route::get('/faqs', [FaqController::class, 'index']);

Route::prefix('locations')->group(function () {
    Route::get('/countries', [LocationController::class, 'countries']);
    Route::get('/states/{country_id}', [LocationController::class, 'states']);
    Route::get('/cities/{state_id}', [LocationController::class, 'cities']);
});

Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{blog:slug}', [BlogController::class, 'show']);
});

Route::get('/policies/{policy}', [PolicyController::class, 'show']);

Route::prefix('setting')->group(function () {
    Route::get('/footer', [SettingController::class, 'getFooterSettings']);
    Route::get('/{key}', [SettingController::class, 'getByKey']);
});

Route::get('/scenes', [SceneController::class, 'customerIndex']);
