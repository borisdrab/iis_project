<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginUser;
use App\Http\Controllers\MiestoController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UdalostController;
use App\Http\Controllers\KalendarController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\UdalostFotoController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\RegistraciaController;
use App\Http\Controllers\KategorieController;

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

    Route::post('/udalosti/{id}/foto', [UdalostFotoController::class, 'store'])->name('udalosti.foto.store');
    Route::delete('/udalosti/foto/{foto}', [UdalostFotoController::class, 'delete'])->name ('udalosti.foto.delete');
    Route::post('/udalosti/{udalost}/registracia', [RegistraciaController::class, 'registrujNaUdalost']);

    Route::delete('/registracie/{registracia}', [RegistraciaController::class, 'zrusRegistraciu']);

    Route::post('/miesta/navrhnut', [MiestoController::class, 'navrhniMiesto']);
    Route::post('/kategorie/navrhnut', [KategorieController::class, 'navrhniKategoriu']);

    Route::patch('/registracie/{registracia}/platba', [RegistraciaController::class, 'potvrdPlatbu']);

    Route::middleware(['role:Administrator'])->prefix('admin')->group(function () {
        Route::inertia('listOfUsers', 'admin/user-list')->name('listOfUsers');
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::patch('/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive']);
    });

    Route::middleware(['role:Moderator'])->prefix('moderator')->group(function () {
        Route::get('/moderation', [ModeratorController::class, 'index'])->name('moderator.moderation');

        Route::patch('/udalosti/{udalost}/schvalit', [ModeratorController::class, 'schvalUdalost']);
        Route::get('/udalosti/cakajuce', [ModeratorController::class, 'cakajuceUdalosti']);
        Route::patch('/udalosti/{udalost}/zamietnut', [ModeratorController::class, 'zamietnutUdalost']);

        Route::get('/miesta/cakajuce', [ModeratorController::class, 'cakajuceMiesta']);
        Route::patch('/miesta/{miesto}/schvalit', [ModeratorController::class, 'schvalMiesto']);
        Route::patch('/miesta/{miesto}/zamietnut', [ModeratorController::class, 'zamietnutMiesto']);

        Route::get('/kategorie/cakajuce', [ModeratorController::class, 'cakajuceKategorie']);
        Route::patch('/kategorie/{kategoria}/schvalit', [ModeratorController::class, 'schvalKategoriu']);
        Route::patch('/kategorie/{kategoria}/zamietnut', [ModeratorController::class, 'zamietnutKategoriu']);

        Route::delete('/komentare/{komentar}', [ModeratorController::class, 'odstranKomentar']);

    });

    Route::post('/logout', [loginUser::class, 'logout']);
});

Route::get('/udalosti/{id}', [UdalostController::class, 'show'])
    ->name('udalosti.show');

Route::get('/login', [loginUser::class, 'show'])->name('login');
Route::post('/login', [loginUser::class, 'authenticate'])->middleware('throttle:10,1');
Route::inertia('/register', 'auth/register')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');