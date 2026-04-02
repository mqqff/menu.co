<?php

use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\RecipeController;

Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.show-register');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.show-login');
});

Route::prefix('recipes')->group(function () {
    Route::get('/my', [RecipeController::class, 'my'])->name('recipes.my');
   Route:: get('/trending/category', [RecipeController::class, 'trendingByCategory'])->name('recipes.trending.category');
});

Route::resource('recipes', RecipeController::class);
