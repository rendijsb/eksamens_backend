<?php

declare(strict_types=1);

use App\Http\Controllers\Banners\BannerController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Images\ImageController;
use App\Http\Controllers\Products\ProductController;
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
        Route::post('edit/{categoryId}', [CategoryController::class, 'editCategory']);
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
    ->prefix('images')
    ->group(function () {
        Route::get('{relatedId}', [ImageController::class, 'getImages']);
        Route::post('upload/{relatedId}', [ImageController::class, 'uploadImages']);
        Route::patch('set-primary/{imageId}', [ImageController::class, 'setPrimaryImage']);
        Route::delete('delete/{imageId}', [ImageController::class, 'deleteImage']);
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('banners')
    ->group(function () {
        Route::get('getAll', [BannerController::class, 'getAllBanners']);
        Route::post('create', [BannerController::class, 'createBanner']);
        Route::get('{bannerId}', [BannerController::class, 'getBannerById']);
        Route::post('edit/{bannerId}', [BannerController::class, 'editBanner']);
        Route::delete('delete/{bannerId}', [BannerController::class, 'deleteBanner']);
    });

Route::prefix('public')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('getAllActiveCategories', [CategoryController::class, 'getAllActiveCategories']);
    });

    Route::prefix('products')->group(function () {
        Route::get('getAllPopularActiveProducts', [ProductController::class, 'getAllPopularActiveProducts']);
    });

    Route::prefix('banners')->group(function () {
        Route::get('getAllActiveBanners', [BannerController::class, 'getAllActiveBanners']);
    });
});
