<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TodoPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;


// User Routes
Route::get('/user', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Global Routes
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}/products', [CategoryController::class, 'getProductsByCategoryId']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/favorites', [ProductController::class, 'favoriteProducts']);
Route::get('/toggle/{product_id}/favorite', [ProductController::class, 'toggleFavorite']);
Route::get('/product/stock', [ProductController::class, 'checkStock']);
Route::get('/product/search/{text}', [ProductController::class, 'search']);
Route::get('/product/price/asc', [ProductController::class, 'sortByPriceAsc']);
Route::get('/product/price/desc', [ProductController::class, 'sortByPriceDesc']);

// Forget/Reset Password Routes
// Send reset link
Route::get('/forgot-password', [TodoPasswordController::class, 'sendResetLinkEmail']);
// Reset password
Route::post('/reset-password', [TodoPasswordController::class, 'updatePassword']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::post('/logout-currently', [UserController::class, 'logoutCurrent']);
        Route::post('/logoutall', [UserController::class, 'logoutAll']);
        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::get('/cart', [UserController::class, 'getCart']);
        // Forgot Password Routes
        Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
        Route::post('/reset-password', [UserController::class, 'resetPassword']);
    });

    Route::prefix('/category')->group(function () {
        Route::post('/create', [CategoryController::class, 'store']);
        Route::post('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
    });

    Route::prefix('/product')->group(function () {

        Route::post('/create', [ProductController::class, 'store']);
        Route::get('/toggle/{product_id}/favorite', [ProductController::class, 'toggleFavorite']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::delete('/delete/{id}', [ProductController::class, 'destroy']);
    });

    // Cart Routes
    Route::prefix('/cart')->group(function () {
        Route::get('/', [CartController::class, 'getActiveCart']);
        Route::post('/', [CartController::class, 'store']);
    });

    // CartItems Route
    Route::prefix('/cart-item')->group(function () {
        Route::get('/', [CartItemController::class, 'index']);
        Route::post('/', [CartItemController::class, 'addToCart']);
    });
});
