<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

// Public Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

// Protected Routes
Route::middleware('auth:api')->group(function () {
    // Auth Routes
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('user', [AuthController::class, 'user'])->name('user');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    });

    // Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrdersController::class, 'index'])->name('index');
        Route::post('/', [OrdersController::class, 'store'])->name('store');
        Route::get('/{id}', [OrdersController::class, 'show'])
            ->whereNumber('id')
            ->name('show');
        Route::patch('/{id}', [OrdersController::class, 'update'])
            ->whereNumber('id')
            ->name('update');
        Route::patch('/{id}/status', [OrdersController::class, 'updateStatus'])
            ->whereNumber('id')
            ->name('updateStatus');
    });
});
