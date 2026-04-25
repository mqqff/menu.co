<?php

use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\RecipeController;
use \App\Http\Controllers\ProfileController;

Route::redirect('/', '/recipes')->name('home');

Route::prefix('profile')->group(function () {
    Route::get('/settings', [ProfileController::class, 'edit'])->middleware('auth')->name('settings');
    Route::get('/{user:username}', [ProfileController::class, 'show'])->middleware('auth')->name('profile');
    Route::put('/{user}', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
    Route::delete('/{user}', [ProfileController::class, 'destroy'])->middleware('auth')->name('profile.destroy');
});

Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('show-register');
    Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('show-login');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
});

Route::prefix('recipes')->group(function () {
   Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
   Route::middleware('auth')->group(function () {
        Route::get('/my', [RecipeController::class, 'my'])->name('recipes.my');
        Route::get('/create', [RecipeController::class, 'create'])->name('recipes.create');
        Route::post('/', [RecipeController::class, 'store'])->name('recipes.store');
        Route::get('/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
        Route::put('/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
   });
   Route::get('/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

   Route::get('/trending', [RecipeController::class, 'trending'])->name('recipes.trending');
   Route:: get('/trending/category', [RecipeController::class, 'trendingByCategory'])->name('recipes.trending.category');
});

