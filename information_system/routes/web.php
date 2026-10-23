<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginUser;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminUserController;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::middleware(['role:Administrator'])->prefix('admin')->group(function (){
        Route::inertia('listOfUsers', 'listOfUsers')->name('listOfUsers');
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::patch('/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive']);
    }
    );
});
Route::post('/login', [loginUser::class,'authenticate']);
Route::get('/login', [loginUser::class, 'show'])->name('login')->middleware('throttle:10,1');
Route::post('/logout', [loginUser::class, 'logout']);
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
