<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/validate', [AuthController::class, 'validateToken']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/products/all', [ProductController::class, 'all']);
    Route::apiResource('products', ProductController::class);

    Route::middleware('role:ADMIN')->group(function () {

        Route::apiResource(
            'roles',
            RoleController::class
        );

        Route::put('/roles/desactivate/{id}', [
            RoleController::class,
            'desactivate'
        ]);

        Route::put('/roles/activate/{id}', [
            RoleController::class,
            'activate'
        ]);

        Route::apiResource(
            'users',
            UserController::class
        );

        Route::post('/users/add-roles/{id}', [
            UserController::class,
            'addRoles'
        ]);

        Route::post('/users/remove-roles/{id}', [
            UserController::class,
            'removeRoles'
        ]);

        Route::put('/users/desactivate/{id}', [
            UserController::class,
            'desactivate'
        ]);

        Route::put('/users/activate/{id}', [
            UserController::class,
            'activate'
        ]);
    });
});