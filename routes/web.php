<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/health-check', fn () => response()->json(['ok' => true, 'app' => config('app.name')]))->name('health-check');
