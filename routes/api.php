<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// User Routes
Route::get('/user', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Global Routes
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}/products', [CategoryController::class, 'getProductsByCategoryId']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/favorites', [ProductController::class, 'favoriteProducts']);
Route::get('/product/toggle/{product_id}/favorite', [ProductController::class, 'toggleFavorite']);
Route::get('/product/stock', [ProductController::class, 'checkStock']);
Route::get('/product/search/{text}', [ProductController::class, 'search']);
Route::get('/product/price/asc', [ProductController::class, 'sortByPriceAsc']);
Route::get('/product/price/desc', [ProductController::class, 'sortByPriceDesc']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::post('/logout-currently', [UserController::class, 'logoutCurrent']);
        Route::post('/logoutall', [UserController::class, 'logoutAll']);
        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
    });

    Route::prefix('/category')->group(function () {
        Route::post('/create', [CategoryController::class, 'store']);
        Route::post('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
    });

    Route::prefix('/product')->group(function () {

        Route::post('/create', [ProductController::class, 'store']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::delete('/delete/{id}', [ProductController::class, 'destroy']);
    });
});
