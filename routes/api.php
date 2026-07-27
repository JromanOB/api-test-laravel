<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
//use Illuminate\Http\Request;

Route::apiResource('products', ProductController::class);
