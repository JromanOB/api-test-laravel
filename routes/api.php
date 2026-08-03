<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/validate', [
    AuthController::class,
    'validateToken'
]);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});


Route::get('/products/all', [ProductController::class, 'all']);
Route::apiResource('products', ProductController::class);
Route::apiResource('users', UserController::class);

Route::apiResource('auth', AuthController::class);
