<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

// User routes
Route::middleware('auth:api')->group(function () {
    Route::get('get-user', [AuthController::class, 'getUser']);
    Route::put('/user/address', [UserController::class, 'updateAddress']);
});

// Product routes
Route::middleware('auth:api')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
