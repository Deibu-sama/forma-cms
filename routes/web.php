<?php

use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/health-check', fn () => response()->json(['ok' => true, 'app' => config('app.name')]))->name('health-check');
