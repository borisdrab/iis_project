<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginUser;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth','role:Administrator'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

Route::post('/login', [loginUser::class,'authenticate']);
Route::get('/login', [loginUser::class, 'show'])->name('login');
Route::post('/logout', [loginUser::class, 'logout']);
