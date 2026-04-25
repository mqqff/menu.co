<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController;

Route::redirect('/', '/recipes')->name('home');

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::prefix('profile')->group(function () {
        Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.settings');
        Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/{user}', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/{user}', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('recipes')->group(function () {
        Route::get('/my', [RecipeController::class, 'my'])->name('recipes.my');
        Route::get('/create', [RecipeController::class, 'create'])->name('recipes.create');
        Route::post('/', [RecipeController::class, 'store'])->name('recipes.store');

        Route::get('/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
        Route::put('/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

        Route::post('/{recipe}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    });
});

Route::prefix('recipes')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/trending', [RecipeController::class, 'trending'])->name('recipes.trending');
    Route::get('/trending/category', [RecipeController::class, 'trendingByCategory'])->name('recipes.trending.category');
    Route::get('/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
});
