<?php

declare(strict_types=1);

use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Middleware\CheckRoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});

Route::prefix('user')->group(function () {
    Route::get('getAll', [UserController::class, 'getAll'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin'
        ]);
    Route::delete('delete/{userId}', [UserController::class, 'deleteUser'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin'
        ]);
    Route::get('{userId}', [UserController::class, 'getUserById'])
        ->middleware(['auth:sanctum']);
    Route::post('create', [UserController::class, 'createUser'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin'
        ]);
    Route::patch('edit/{userId}', [UserController::class, 'editUser'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin'
        ]);
});

Route::prefix('categories')->group(function () {
    Route::get('getAll', [CategoryController::class, 'getAllCategories'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin|moderator',
        ]);
    Route::post('create', [CategoryController::class, 'createCategory'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin|moderator',
        ]);
    Route::get('{categoryId}', [CategoryController::class, 'getCategoryById'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin|moderator',
        ]);
    Route::patch('edit/{categoryId}', [CategoryController::class, 'editCategory'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin|moderator',
        ]);
    Route::delete('delete/{categoryId}', [CategoryController::class, 'deleteCategory'])
        ->middleware([
            'auth:sanctum',
            CheckRoleMiddleware::class . ':admin|moderator',
        ]);
});
