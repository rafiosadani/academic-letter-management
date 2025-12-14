<?php

namespace App\Enums;

enum PermissionName: string
{
    // ============================================
    // DASHBOARD
    // ============================================
    case DASHBOARD_VIEW = 'dashboard.view';

    // ============================================
    // MASTER DATA
    // ============================================

    // User Management
    case MASTER_USER_VIEW = 'master.user.view';
    case MASTER_USER_CREATE = 'master.user.create';
    case MASTER_USER_UPDATE = 'master.user.update';
    case MASTER_USER_DELETE = 'master.user.delete';

    // Role Management
    case MASTER_ROLE_VIEW = 'master.role.view';
    case MASTER_ROLE_CREATE = 'master.role.create';
    case MASTER_ROLE_UPDATE = 'master.role.update';
    case MASTER_ROLE_DELETE = 'master.role.delete';

    // Study Programs (Program Studi)
    case MASTER_STUDY_PROGRAM_VIEW = 'master.study_program.view';
    case MASTER_STUDY_PROGRAM_CREATE = 'master.study_program.create';
    case MASTER_STUDY_PROGRAM_UPDATE = 'master.study_program.update';
    case MASTER_STUDY_PROGRAM_DELETE = 'master.study_program.delete';

    // Academic Years (Tahun Akademik)
    case MASTER_ACADEMIC_YEAR_VIEW = 'master.academic.year.view';
    case MASTER_ACADEMIC_YEAR_CREATE = 'master.academic.year.create';
    case MASTER_ACADEMIC_YEAR_UPDATE = 'master.academic.year.update';
    case MASTER_ACADEMIC_YEAR_DELETE = 'master.academic.year.delete';

    // Semesters (Semester)
    case MASTER_SEMESTER_VIEW = 'master.semester.view';
    case MASTER_SEMESTER_CREATE = 'master.semester.create';
    case MASTER_SEMESTER_UPDATE = 'master.semester.update';
    case MASTER_SEMESTER_DELETE = 'master.semester.delete';

    // Faculty Officials (Penugasan Jabatan)
    case MASTER_FACULTY_OFFICIAL_VIEW = 'master.faculty_official.view';
    case MASTER_FACULTY_OFFICIAL_CREATE = 'master.faculty_official.create';
    case MASTER_FACULTY_OFFICIAL_UPDATE = 'master.faculty_official.update';
    case MASTER_FACULTY_OFFICIAL_DELETE = 'master.faculty_official.delete';

    // ============================================
    // LETTER (SURAT)
    // ============================================

    // My Letters (Surat Saya - Mahasiswa)
    case LETTER_MY_VIEW = 'letter.my.view';
    case LETTER_MY_CREATE = 'letter.my.create';

    // Incoming Letters (Surat Masuk - for Approvers)
    case LETTER_INCOMING_VIEW = 'letter.incoming.view';

    // Manage Letters (Kelola Semua Surat - for Staff/Admin)
    case LETTER_MANAGE_VIEW = 'letter.manage.view';
    case LETTER_MANAGE_UPDATE = 'letter.manage.update';

    // Letter Actions
    case LETTER_APPROVE = 'letter.approve';
    case LETTER_REJECT = 'letter.reject';

    // ============================================
    // NOTIFICATION
    // ============================================
    case NOTIFICATION_VIEW = 'notification.view';

    // ============================================
    // REPORT (LAPORAN)
    // ============================================
    case REPORT_STATISTIC_VIEW = 'report.statistic.view';
    case REPORT_TRACKING_VIEW = 'report.tracking.view';
    case REPORT_EXPORT = 'report.export';

    // ============================================
    // SETTINGS (PENGATURAN)
    // ============================================

    // General Settings (Pengaturan Umum)
    case SETTINGS_GENERAL_VIEW = 'settings.general.view';
    case SETTINGS_GENERAL_UPDATE = 'settings.general.update';

    // Approval Flow (Alur Persetujuan)
    case SETTINGS_APPROVAL_FLOW_VIEW = 'settings.approval_flow.view';
    case SETTINGS_APPROVAL_FLOW_CREATE = 'settings.approval_flow.create';
    case SETTINGS_APPROVAL_FLOW_UPDATE = 'settings.approval_flow.update';
    case SETTINGS_APPROVAL_FLOW_DELETE = 'settings.approval_flow.delete';

    // Letter Number Config (Konfigurasi Nomor Surat)
    case SETTINGS_LETTER_NUMBER_VIEW = 'settings.letter_number.view';
    case SETTINGS_LETTER_NUMBER_CREATE = 'settings.letter_number.create';
    case SETTINGS_LETTER_NUMBER_UPDATE = 'settings.letter_number.update';
    case SETTINGS_LETTER_NUMBER_DELETE = 'settings.letter_number.delete';

    // Notification Settings (Pengaturan Notifikasi)
    case SETTINGS_NOTIFICATION_VIEW = 'settings.notification.view';
    case SETTINGS_NOTIFICATION_UPDATE = 'settings.notification.update';

    // ============================================
    // PROFILE
    // ============================================
    case PROFILE_VIEW = 'profile.view';
    case PROFILE_UPDATE = 'profile.update';

    // ============================================
    // HELPER METHODS
    // ============================================

    public function groupName(): string
    {
        $main = explode('.', $this->value)[0]; // master, surat, laporan, dll.

        return match ($main) {
            'dashboard'     => 'Dashboard',
            'master'        => 'Master Data',
            'letter'        => 'Surat',
            'notification'  => 'Notifikasi',
            'report'        => 'Laporan',
            'settings'      => 'Pengaturan',
            'profile'       => 'Profil',
            default         => ucfirst($main)
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            // Dashboard
            self::DASHBOARD_VIEW => 'Lihat Dashboard',

            // Master - User
            self::MASTER_USER_VIEW => 'Lihat Pengguna',
            self::MASTER_USER_CREATE => 'Tambah Pengguna',
            self::MASTER_USER_UPDATE => 'Edit Pengguna',
            self::MASTER_USER_DELETE => 'Hapus Pengguna',

            // Master - Role
            self::MASTER_ROLE_VIEW => 'Lihat Role & Permission',
            self::MASTER_ROLE_CREATE => 'Tambah Role',
            self::MASTER_ROLE_UPDATE => 'Edit Role',
            self::MASTER_ROLE_DELETE => 'Hapus Role',

            // Master - Study Program
            self::MASTER_STUDY_PROGRAM_VIEW => 'Lihat Program Studi',
            self::MASTER_STUDY_PROGRAM_CREATE => 'Tambah Program Studi',
            self::MASTER_STUDY_PROGRAM_UPDATE => 'Edit Program Studi',
            self::MASTER_STUDY_PROGRAM_DELETE => 'Hapus Program Studi',

            // Master - Academic Year
            self::MASTER_ACADEMIC_YEAR_VIEW => 'Lihat Tahun Akademik',
            self::MASTER_ACADEMIC_YEAR_CREATE => 'Tambah Tahun Akademik',
            self::MASTER_ACADEMIC_YEAR_UPDATE => 'Edit Tahun Akademik',
            self::MASTER_ACADEMIC_YEAR_DELETE => 'Hapus Tahun Akademik',

            // Master - Semester
            self::MASTER_SEMESTER_VIEW => 'Lihat Semester',
            self::MASTER_SEMESTER_CREATE => 'Tambah Semester',
            self::MASTER_SEMESTER_UPDATE => 'Edit Semester',
            self::MASTER_SEMESTER_DELETE => 'Hapus Semester',

            // Master - Faculty Official
            self::MASTER_FACULTY_OFFICIAL_VIEW => 'Lihat Penugasan Jabatan',
            self::MASTER_FACULTY_OFFICIAL_CREATE => 'Tambah Penugasan Jabatan',
            self::MASTER_FACULTY_OFFICIAL_UPDATE => 'Edit Penugasan Jabatan',
            self::MASTER_FACULTY_OFFICIAL_DELETE => 'Hapus Penugasan Jabatan',

            // Letter - My Letters
            self::LETTER_MY_VIEW => 'Lihat Surat Saya',
            self::LETTER_MY_CREATE => 'Ajukan Surat Baru',

            // Letter - Incoming
            self::LETTER_INCOMING_VIEW => 'Lihat Surat Masuk',

            // Letter - Manage
            self::LETTER_MANAGE_VIEW => 'Lihat Semua Surat',
            self::LETTER_MANAGE_UPDATE => 'Kelola Surat',

            // Letter - Actions
            self::LETTER_APPROVE => 'Approve Surat',
            self::LETTER_REJECT => 'Reject Surat',

            // Notification
            self::NOTIFICATION_VIEW => 'Lihat Notifikasi',

            // Report
            self::REPORT_STATISTIC_VIEW => 'Lihat Statistik Surat',
            self::REPORT_TRACKING_VIEW => 'Lihat Tracking Approval',
            self::REPORT_EXPORT => 'Export Data Laporan',

            // Settings - General
            self::SETTINGS_GENERAL_VIEW => 'Lihat Pengaturan Umum',
            self::SETTINGS_GENERAL_UPDATE => 'Edit Pengaturan Umum',

            // Settings - Approval Flow
            self::SETTINGS_APPROVAL_FLOW_VIEW => 'Lihat Alur Persetujuan',
            self::SETTINGS_APPROVAL_FLOW_CREATE => 'Tambah Alur Persetujuan',
            self::SETTINGS_APPROVAL_FLOW_UPDATE => 'Edit Alur Persetujuan',
            self::SETTINGS_APPROVAL_FLOW_DELETE => 'Hapus Alur Persetujuan',

            // Settings - Letter Number
            self::SETTINGS_LETTER_NUMBER_VIEW => 'Lihat Konfigurasi Nomor Surat',
            self::SETTINGS_LETTER_NUMBER_CREATE => 'Tambah Konfigurasi Nomor Surat',
            self::SETTINGS_LETTER_NUMBER_UPDATE => 'Edit Konfigurasi Nomor Surat',
            self::SETTINGS_LETTER_NUMBER_DELETE => 'Hapus Konfigurasi Nomor Surat',

            // Settings - Notification
            self::SETTINGS_NOTIFICATION_VIEW => 'Lihat Pengaturan Notifikasi',
            self::SETTINGS_NOTIFICATION_UPDATE => 'Edit Pengaturan Notifikasi',

            // Profile
            self::PROFILE_VIEW => 'Lihat Profil',
            self::PROFILE_UPDATE => 'Edit Profil',
        };
    }
}
