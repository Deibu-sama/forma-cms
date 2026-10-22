<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

Route::get('/health-check', fn () => response()->json(['ok' => true, 'app' => config('app.name')]))->name('health-check');
