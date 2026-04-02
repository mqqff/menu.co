<?php

use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\RecipeController;

Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.show-register');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.show-login');
});

Route::resource('recipes', RecipeController::class);
Route::get('/my-recipes', [RecipeController::class, 'my'])->name('recipes.my');
