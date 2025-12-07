<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Master\AcademicYearController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SemesterController;
use App\Http\Controllers\Master\StudyProgramController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationSettingController;
use Illuminate\Support\Facades\Route;


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

    // ============================================================
    // MASTER DATA ROUTES
    // ============================================================

    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        // Roles
        Route::middleware(['permission:master.role.view,master.role.create,master.role.update,master.role.delete'])
            ->group(function () {
                Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
                Route::post('roles/restore-all', [RoleController::class, 'restoreAll'])->name('roles.restore.all');
                Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete');
                Route::resource('/roles', RoleController::class)->names('roles');
            });

        // Users
        Route::middleware(['permission:master.user.view,master.user.create,master.user.update,master.user.delete'])
            ->group(function () {
                Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
                Route::post('users/restore-all', [UserController::class, 'restoreAll'])->name('users.restore.all');
                Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
                Route::patch('users/{user}/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
                Route::resource('/users', UserController::class)->names('users');
            });

        // Study Programs
        Route::post('study-programs/{id}/restore', [StudyProgramController::class, 'restore'])->name('study-programs.restore');
        Route::post('study-programs/restore-all', [StudyProgramController::class, 'restoreAll'])->name('study-programs.restore.all');
        Route::delete('study-programs/{id}/force-delete', [StudyProgramController::class, 'forceDelete'])->name('study-programs.force-delete');
        Route::resource('/study-programs', StudyProgramController::class)->names('study-programs');

        // Academic Years
        Route::resource('/academic-years', AcademicYearController::class)->names('academic-years');

        // Semesters
        //Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index');
        Route::post('semesters/{semester}/toggle-active', [SemesterController::class, 'toggleActive'])->name('semesters.toggle-active');
        Route::resource('semesters', SemesterController::class)->only(['index'])->names(['index' => 'semesters.index']);
        Route::resource('semesters', SemesterController::class)->only(['index'])->names('semesters');
    });

    // ============================================================
    // NOTIFICATION ROUTES
    // ============================================================

    // Notification Center
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Notification Settings
    Route::get('notifications/settings', [NotificationSettingController::class, 'index'])->name('notifications.settings');
    Route::post('notifications/settings', [NotificationSettingController::class, 'update'])->name('notifications.settings.update');

    // ============================================================
    // NOTIFICATION API ENDPOINTS (Moved from api.php)
    // ============================================================

    Route::prefix('api')->group(function () {
        Route::get('/notifications/check', [NotificationController::class, 'check'])->name('api.notifications.check');
        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-as-read');
    });

    // Logout
    Route::post('/logout', LogoutController::class)->name('logout');
});