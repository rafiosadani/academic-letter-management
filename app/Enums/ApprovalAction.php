<?php

namespace App\Enums;

enum ApprovalAction: string
{
    case TO_STUDENT = 'to_student';
    case TO_PREVIOUS_STEP = 'to_previous_step';
    case TERMINATE = 'terminate';

    /**
     * Get label untuk UI
     */
    public function label(): string
    {
        return match($this) {
            self::TO_STUDENT => 'Kembali ke Mahasiswa',
            self::TO_PREVIOUS_STEP => 'Kembali ke Step Sebelumnya',
            self::TERMINATE => 'Batalkan Total (Buat Baru)',
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::TO_STUDENT => 'Surat dikembalikan ke mahasiswa untuk diperbaiki (jika editable)',
            self::TO_PREVIOUS_STEP => 'Surat kembali ke step approval sebelumnya',
            self::TERMINATE => 'Surat dibatalkan total, mahasiswa harus membuat pengajuan baru',
        };
    }

    /**
     * Get icon
     */
    public function icon(): string
    {
        return match($this) {
            self::TO_STUDENT => 'fa-user',
            self::TO_PREVIOUS_STEP => 'fa-arrow-left',
            self::TERMINATE => 'fa-ban',
        };
    }

    /**
     * Get color for badge
     */
    public function color(): string
    {
        return match($this) {
            self::TO_STUDENT => 'warning',
            self::TO_PREVIOUS_STEP => 'info',
            self::TERMINATE => 'error',
        };
    }
}
