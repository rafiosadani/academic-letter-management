<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->prefix('auth')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard.index'); })->name('dashboard');

    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::get('/auth/register', function () {
    return view('auth.register');
})->name('register');
