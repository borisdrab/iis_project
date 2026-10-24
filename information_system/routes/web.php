<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginUser;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UdalostController;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('/udalosti/create', [UdalostController::class, 'create']);
    Route::post('/udalosti', [UdalostController::class, 'store']);
    Route::get('/udalosti/{udalost}/edit', [UdalostController::class, 'edit']);
    Route::put('/udalosti/{udalost}', [UdalostController::class, 'update']);

    Route::middleware(['role:Administrator'])->prefix('admin')->group(function () {
        Route::inertia('listOfUsers', 'listOfUsers')->name('listOfUsers');
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::patch('/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive']);
    });

    Route::post('/logout', [loginUser::class, 'logout']);
});

Route::get('/login', [loginUser::class, 'show'])->name('login');
Route::post('/login', [loginUser::class, 'authenticate'])->middleware('throttle:10,1');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');