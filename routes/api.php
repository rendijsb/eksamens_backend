<?php

declare(strict_types=1);

use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\ProductImageController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Middleware\CheckRoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::middleware(CheckRoleMiddleware::class . ':admin')->group(function () {
        Route::get('getAll', [UserController::class, 'getAll']);
        Route::delete('delete/{userId}', [UserController::class, 'deleteUser']);
        Route::post('create', [UserController::class, 'createUser']);
        Route::patch('edit/{userId}', [UserController::class, 'editUser']);
    });

    Route::get('{userId}', [UserController::class, 'getUserById']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('categories')
    ->group(function () {
        Route::get('getAll', [CategoryController::class, 'getAllCategories']);
        Route::post('create', [CategoryController::class, 'createCategory']);
        Route::get('{categoryId}', [CategoryController::class, 'getCategoryById']);
        Route::patch('edit/{categoryId}', [CategoryController::class, 'editCategory']);
        Route::delete('delete/{categoryId}', [CategoryController::class, 'deleteCategory']);
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('products')
    ->group(function () {
        Route::get('getAll', [ProductController::class, 'getAllProducts']);
        Route::post('create', [ProductController::class, 'createProduct']);
        Route::get('{productId}', [ProductController::class, 'getProductById']);
        Route::patch('edit/{productId}', [ProductController::class, 'editProduct']);
        Route::delete('delete/{productId}', [ProductController::class, 'deleteProduct']);
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('product-images')
    ->group(function () {
        Route::get('{productId}', [ProductImageController::class, 'getProductImages']);
        Route::post('upload/{productId}', [ProductImageController::class, 'uploadImages']);
        Route::patch('set-primary/{imageId}', [ProductImageController::class, 'setPrimaryImage']);
        Route::delete('delete/{imageId}', [ProductImageController::class, 'deleteImage']);
    });
