<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/auth/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/auth/login', function () {
    return view('auth.login');
})->name('login');
