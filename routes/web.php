<?php

use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.show-register');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.show-login');
});
