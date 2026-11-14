<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginUser;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UdalostController;
use App\Http\Controllers\MiestoController;
use App\Http\Controllers\KalendarController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\UdalostFotoController;

Route::get('/', [UdalostController::class, 'index'])
    ->name('home');

Route::get('/miesta/{id}', [MiestoController::class, 'show'])
    ->name('miesta.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/udalosti/create', [UdalostController::class, 'create']);
    Route::post('/udalosti', [UdalostController::class, 'store']);
    Route::get('/udalosti/{udalost}/edit', [UdalostController::class, 'edit']);
    Route::put('/udalosti/{udalost}', [UdalostController::class, 'update']);

    Route::get('/kalendar', [KalendarController::class, 'index'])
        ->name('kalendar.index');

    Route::post('/udalosti/{id}/komentare', [KomentarController::class, 'store'])
        ->name('komentare.store');

    Route::post('/udalosti/{id}/foto', [UdalostFotoController::class, 'store'])
        ->name('udalosti.foto.store');

    Route::middleware(['role:Administrator'])->prefix('admin')->group(function () {
        Route::inertia('listOfUsers', 'listOfUsers')->name('listOfUsers');
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::patch('/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive']);
    });

    Route::post('/logout', [loginUser::class, 'logout']);
});

Route::get('/udalosti/{id}', [UdalostController::class, 'show'])
    ->name('udalosti.show');

Route::get('/login', [loginUser::class, 'show'])->name('login');
Route::post('/login', [loginUser::class, 'authenticate'])->middleware('throttle:10,1');
Route::inertia('/register', 'auth/register')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');