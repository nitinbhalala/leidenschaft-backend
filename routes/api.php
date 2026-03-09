<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
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
});
