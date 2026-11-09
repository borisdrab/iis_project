<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UdalostController;
use App\Http\Controllers\MiestoController;
use App\Http\Controllers\KalendarController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\UdalostFotoController;

Route::inertia('/', 'welcome')->name('home');

Route::get('/udalosti', [UdalostController::class, 'index'])
    ->name('udalosti.index');

Route::get('/miesta/{id}', [MiestoController::class, 'show'])
    ->name('miesta.show');

Route::get('/udalosti/{id}', [UdalostController::class, 'show'])
    ->name('udalosti.show');

Route::get('/kalendar', [KalendarController::class, 'index'])
    ->middleware(['auth'])
    ->name('kalendar.index');

Route::post('/udalosti/{id}/komentare', [KomentarController::class, 'store'])
    ->middleware(['auth'])
    ->name('komentare.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
