<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Master\RoleController;

Route::get('/', function () {
    return redirect(route('login'));
});

Route::middleware('guest')->prefix('auth')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');

    // Register
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard.index'); })->name('dashboard');

    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        // Roles
        Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
        Route::post('roles/restore-all', [RoleController::class, 'restoreAll'])->name('roles.restore.all');
        Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete');
        Route::resource('/roles', RoleController::class)->names('roles');
    });
    Route::post('/logout', LogoutController::class)->name('logout');
});