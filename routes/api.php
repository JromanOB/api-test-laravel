<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
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


Route::get('/products/all', [ProductController::class, 'all'])
    ->middleware('auth:api');
Route::apiResource('products', ProductController::class);

Route::apiResource('roles', RoleController::class);

Route::apiResource('users', UserController::class);
Route::post('/users/add-roles/{id}', [UserController::class, 'addRoles'])
    ->middleware('auth:api');
Route::post('/users/remove-roles/{id}', [UserController::class, 'removeRoles'])
    ->middleware('auth:api');
Route::put('/users/desactivate/{id}', [UserController::class, 'desactivate']);
Route::put('/users/activate/{id}', [UserController::class, 'activate']);
