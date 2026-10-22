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
        Route::get('/faqs', [AdminController::class, 'faqs'])->name('faqs');
        Route::post('/faqs', [AdminController::class, 'storeFaq'])->name('faqs.store');
        Route::put('/faqs/{index}', [AdminController::class, 'updateFaq'])->whereNumber('index')->name('faqs.update');
        Route::delete('/faqs/{index}', [AdminController::class, 'destroyFaq'])->whereNumber('index')->name('faqs.destroy');
        Route::get('/content/{section}', [AdminController::class, 'editContentSection'])->where('section', 'home|about|services|tax-compliance|experience|skills|how-i-work|packages|why-work-with-me')->name('content.edit');
        Route::put('/content/{section}', [AdminController::class, 'updateContentSection'])->where('section', 'home|about|services|tax-compliance|experience|skills|how-i-work|packages|why-work-with-me')->name('content.update');
        Route::get('/certificates', [AdminController::class, 'certificates'])->name('certificates');
        Route::post('/certificates', [AdminController::class, 'storeCertificate'])->name('certificates.store');
        Route::put('/certificates/{index}', [AdminController::class, 'updateCertificate'])->whereNumber('index')->name('certificates.update');
        Route::delete('/certificates/{index}', [AdminController::class, 'destroyCertificate'])->whereNumber('index')->name('certificates.destroy');
        Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
        Route::post('/projects', [AdminController::class, 'storeProject'])->name('projects.store');
        Route::put('/projects/{index}', [AdminController::class, 'updateProject'])->whereNumber('index')->name('projects.update');
        Route::delete('/projects/{index}', [AdminController::class, 'destroyProject'])->whereNumber('index')->name('projects.destroy');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

Route::get('/health-check', fn () => response()->json(['ok' => true, 'app' => config('app.name')]))->name('health-check');
