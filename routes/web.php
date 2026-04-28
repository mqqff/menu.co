<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;

Route::get('/', [RecipeController::class, 'index'])->name('home');

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/auth/login', fn () => redirect()->route('login'))->name('auth.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    Route::prefix('password')->name('auth.password.')->group(function () {
        Route::get('/forgot', [AuthController::class, 'showForgotPassword'])->name('forgot.form');
        Route::post('/forgot', [AuthController::class, 'sendVerificationCode'])->name('forgot.send');
        Route::get('/verify', [AuthController::class, 'showVerifyCodeForm'])->name('verify.form');
        Route::post('/verify', [AuthController::class, 'verifyCode'])->name('verify.check');
        Route::get('/reset', [AuthController::class, 'showResetPassword'])->name('reset.form');
        Route::patch('/reset', [AuthController::class, 'resetPassword'])->name('reset.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::prefix('profile')->group(function () {
        Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.settings');
        Route::get('/bookmarks', [ProfileController::class, 'bookmarks'])->name('profile.bookmarks');
        Route::patch('/{user}/updateAccount', [ProfileController::class, 'updateAccount'])->name('profile.update.account');
        Route::patch('/{user}/updateProfile', [ProfileController::class, 'updateProfile'])->name('profile.update.profile');
        Route::delete('/{user}', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/{user}/report', [ProfileController::class, 'report'])->name('profile.report');
    });

    Route::prefix('recipes')->group(function () {
        Route::get('/my', [RecipeController::class, 'my'])->name('recipes.my');
        Route::get('/create', [RecipeController::class, 'create'])->name('recipes.create');
        Route::post('/', [RecipeController::class, 'store'])->name('recipes.store');
        Route::post('/{recipe}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
        Route::post('/{recipe}/comment', [CommentController::class, 'store'])->name('comments.store');
        Route::post('/{recipe}/report', [RecipeController::class, 'report'])->name('recipes.report');
    });

    Route::prefix('recipes/{recipe}')->middleware('recipe.owner')->group(function () {
        Route::get('/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
        Route::patch('/', [RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    });

    Route::prefix('comments/{comment}')->group(function () {
        Route::delete('/', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/report', [CommentController::class, 'report'])->name('comments.report');
    });
});

Route::prefix('profile/{user:username}')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/recipes', [ProfileController::class, 'createdRecipes'])->name('profile.recipes');
});

Route::prefix('recipes')->group(function () {
    Route::get('/search', [RecipeController::class, 'search'])->name('recipes.search');
    Route::get('/trending', [RecipeController::class, 'trendingRecipes'])->name('recipes.trending');
    Route::get('/trending/category', [RecipeController::class, 'trendingCategories'])->name('recipes.trending.categories');
    Route::get('/recent', [RecipeController::class, 'recentlyAdded'])->name('recipes.recent');
    Route::get('/category/{category:slug}', [RecipeController::class, 'recipeByCategory'])->name('recipes.byCategory');
    Route::get('/{recipe}', [RecipeController::class, 'show'])->middleware('recipe.access')->name('recipes.show');
});
