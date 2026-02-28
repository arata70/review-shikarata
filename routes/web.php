<?php

use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReviewController::class, 'create'])->name('review.create');
Route::get('/review', [ReviewController::class, 'create']);
Route::post('/review', [ReviewController::class, 'store'])->name('review.store');

Route::get('/overlay', [QueueController::class, 'overlay'])->name('overlay.index');
Route::get('/queue/data', [QueueController::class, 'index'])->name('queue.data');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    if (config('auth.allow_registration')) {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    }
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/admin/reviews/data', [AdminReviewController::class, 'data'])->name('admin.reviews.data');
    Route::delete('/admin/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
});
