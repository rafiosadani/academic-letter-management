<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case ACADEMIC_YEAR = 'academic_year';
    case SEMESTER = 'semester';
    case LETTER_APPROVAL = 'letter_approval';
    case LETTER_STATUS = 'letter_status';
    case SYSTEM = 'system';
    case USER_ACCOUNT = 'user_account';

    public function label(): string
    {
        return match($this) {
            self::ACADEMIC_YEAR => 'Tahun Akademik',
            self::SEMESTER => 'Semester',
            self::LETTER_APPROVAL => 'Persetujuan Surat',
            self::LETTER_STATUS => 'Status Surat',
            self::SYSTEM => 'Sistem',
            self::USER_ACCOUNT => 'Akun Pengguna',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ACADEMIC_YEAR => 'Notifikasi terkait tahun akademik',
            self::SEMESTER => 'Notifikasi terkait semester',
            self::LETTER_APPROVAL => 'Notifikasi persetujuan surat',
            self::LETTER_STATUS => 'Notifikasi status surat',
            self::SYSTEM => 'Notifikasi sistem',
            self::USER_ACCOUNT => 'Notifikasi akun pengguna',
        };
    }

    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
