<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Approval\ApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Letter\LetterRequestController;
use App\Http\Controllers\Master\AcademicYearController;
use App\Http\Controllers\Master\FacultyOfficialController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SemesterController;
use App\Http\Controllers\Master\StudyProgramController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationSettingController;
use App\Http\Controllers\PDF\PDFController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Setting\ApprovalFlowController;
use App\Http\Controllers\Setting\LetterNumberConfigController;
use App\Http\Controllers\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('login'));
});

Route::middleware('guest')->prefix('auth')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {

    // ============================================================
    // DASHBOARD ROUTES
    // ============================================================

    Route::middleware(['permission:' . PermissionName::DASHBOARD_VIEW->value])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    });

    // ============================================================
    // MASTER DATA ROUTES
    // ============================================================

    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        // Roles & Permission
        Route::middleware(['permission:' . PermissionName::MASTER_ROLE_VIEW->value])->group(function () {
            Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
            Route::post('roles/restore-all', [RoleController::class, 'restoreAll'])->name('roles.restore.all');
            Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete');
            Route::resource('/roles', RoleController::class)->names('roles');
        });

        // Users Management
        Route::middleware(['permission:' . PermissionName::MASTER_USER_VIEW->value])->group(function () {
            Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::post('users/restore-all', [UserController::class, 'restoreAll'])->name('users.restore.all');
            Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
            Route::patch('users/{user}/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
            Route::resource('/users', UserController::class)->names('users');
        });

        // Study Programs
        Route::middleware(['permission:' . PermissionName::MASTER_STUDY_PROGRAM_VIEW->value])->group(function () {
            Route::post('study-programs/{id}/restore', [StudyProgramController::class, 'restore'])->name('study-programs.restore');
            Route::post('study-programs/restore-all', [StudyProgramController::class, 'restoreAll'])->name('study-programs.restore.all');
            Route::delete('study-programs/{id}/force-delete', [StudyProgramController::class, 'forceDelete'])->name('study-programs.force-delete');
            Route::resource('/study-programs', StudyProgramController::class)->names('study-programs');
        });

        // Academic Years
        Route::middleware(['permission:' . PermissionName::MASTER_ACADEMIC_YEAR_VIEW->value])->group(function () {
            Route::resource('/academic-years', AcademicYearController::class)->names('academic-years');
        });

        // Semesters
        Route::middleware(['permission:' . PermissionName::MASTER_SEMESTER_VIEW->value])->group(function () {
            Route::post('semesters/{semester}/toggle-active', [SemesterController::class, 'toggleActive'])->name('semesters.toggle-active');
            Route::resource('semesters', SemesterController::class)->only(['index'])->names('semesters');
        });

        // Faculty Officials (Penugasan Jabatan)
        Route::middleware(['permission:' . PermissionName::MASTER_FACULTY_OFFICIAL_VIEW->value])->group(function () {
            Route::post('faculty-officials/{id}/restore', [FacultyOfficialController::class, 'restore'])->name('faculty-officials.restore');
            Route::post('faculty-officials/restore-all', [FacultyOfficialController::class, 'restoreAll'])->name('faculty-officials.restore.all');
            Route::delete('faculty-officials/{id}/force-delete', [FacultyOfficialController::class, 'forceDelete'])->name('faculty-officials.force-delete');
            Route::resource('/faculty-officials', FacultyOfficialController::class)->names('faculty-officials');
        });
    });

    // ============================================================
    // SETTINGS ROUTES
    // ============================================================

    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        // General Settings
        Route::middleware(['permission:' . PermissionName::SETTINGS_GENERAL_VIEW->value])->group(function () {
            Route::get('general', [SettingController::class, 'edit'])->name('general.edit');
            Route::put('general', [SettingController::class, 'update'])->name('general.update');
        });

        // Approval Flow Settings
        Route::middleware(['permission:' . PermissionName::SETTINGS_APPROVAL_FLOW_VIEW->value])->group(function () {
            Route::resource('/approval-flows', ApprovalFlowController::class)->names('approval-flows');
        });

        // Letter Number Settings
        Route::middleware(['permission:' . PermissionName::SETTINGS_LETTER_NUMBER_VIEW->value])->group(function () {
            Route::resource('/letter-number-configs', LetterNumberConfigController::class)->except(['show']);
        });
    });

    // ============================================================
    // LETTER (SURAT) & APPROVAL ROUTES
    // ============================================================

    // My Letters (Mahasiswa / Pemohon)
    Route::middleware(['permission:' . PermissionName::LETTER_MY_VIEW->value])->group(function () {
        Route::resource('letters', LetterRequestController::class)->except(['destroy']);
        Route::post('letters/{letter}/cancel', [LetterRequestController::class, 'cancel'])->name('letters.cancel');
        Route::delete('letters/{letter}', [LetterRequestController::class, 'destroy'])->name('letters.destroy');
    });

    // Approval (Incoming / Surat Masuk)
    Route::middleware(['permission:' . PermissionName::LETTER_INCOMING_VIEW->value])->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('approvals/{approval}', [ApprovalController::class, 'show'])->name('approvals.show');

        // Actions (Approve/Reject)
        Route::post('approvals/{approval}/approve', [ApprovalController::class, 'approve'])
            ->middleware(['permission:' . PermissionName::LETTER_APPROVE->value])->name('approvals.approve');
        Route::post('approvals/{approval}/reject', [ApprovalController::class, 'reject'])
            ->middleware(['permission:' . PermissionName::LETTER_REJECT->value])->name('approvals.reject');

        Route::post('approvals/{approval}/edit-content', [ApprovalController::class, 'editContent'])->name('approvals.edit-content');
    });

    // Shared Letter Tools (Downloads & Previews)
    Route::get('letters/{letter}/download-docx', [LetterRequestController::class, 'downloadDocx'])->name('letters.download-docx');
    Route::get('letters/{letter}/download-pdf', [LetterRequestController::class, 'downloadPdf'])->name('letters.download-pdf');
    Route::post('letters/{letter}/upload-pdf', [LetterRequestController::class, 'uploadFinalPdf'])->name('letters.upload-pdf');
    Route::get('approvals/preview-pdf/{letter}', [PDFController::class, 'preview'])->name('approvals.preview-pdf');

    // ============================================================
    // NOTIFICATION ROUTES
    // ============================================================

    Route::middleware(['permission:' . PermissionName::NOTIFICATION_VIEW->value])->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');

        // Notification Settings
        Route::middleware(['permission:' . PermissionName::NOTIFICATION_SETTINGS_VIEW->value])->group(function () {
            Route::get('notifications/settings', [NotificationSettingController::class, 'index'])->name('notifications.settings');
        });
        Route::middleware(['permission:' . PermissionName::NOTIFICATION_SETTINGS_UPDATE->value])->group(function () {
            Route::post('notifications/settings', [NotificationSettingController::class, 'update'])->name('notifications.settings.update');
        });
    });

    // API Notifications (Check & Mark as Read)
    Route::prefix('api')->group(function () {
        Route::get('/notifications/check', [NotificationController::class, 'check'])->name('api.notifications.check');
        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-as-read');
    });

    // ============================================================
    // DOCUMENT ROUTES
    // ============================================================

    Route::post('documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::post('documents/upload-external', [DocumentController::class, 'uploadExternal'])->name('documents.upload-external');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('documents/{document}/stream', [DocumentController::class, 'stream'])->name('documents.stream');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // ============================================================
    // PROFIlES ROUTES
    // ============================================================

    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::middleware(['permission:' . PermissionName::PROFILE_VIEW->value])->group(function () {
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        });

        Route::middleware(['permission:' . PermissionName::PROFILE_UPDATE->value])->group(function () {
            Route::put('/update', [ProfileController::class, 'updateProfile'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        });
    });

    // ============================================================
    // LOGOUT
    // ============================================================
    Route::post('/logout', LogoutController::class)->name('logout');
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Authentication Required)
|--------------------------------------------------------------------------
| Document verification is public so anyone can scan QR code and verify.
*/
//Route::get('verify/{hash}', [DocumentController::class, 'verify'])->name('documents.verify');
Route::get('/documents/verify/{hash}', [DocumentController::class, 'verify'])->name('documents.verify');
Route::get('/documents/download-verified/{hash}', [DocumentController::class, 'downloadVerified'])->name('documents.download-verified');