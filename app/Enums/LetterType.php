<?php

namespace App\Enums;

enum LetterType: string
{
    case SKAK = 'skak';
    case SKAK_TUNJANGAN = 'skak_tunjangan';
    case PENELITIAN = 'penelitian';
    case DISPENSASI_KULIAH = 'dispensasi_kuliah';
    case DISPENSASI_MAHASISWA = 'dispensasi_mahasiswa';

    /**
     * Get label untuk UI
     */
    public function label(): string
    {
        return match($this) {
            self::SKAK => 'Surat Keterangan Aktif Kuliah',
            self::SKAK_TUNJANGAN => 'Surat Keterangan Aktif Kuliah untuk Tunjangan Orang Tua',
            self::PENELITIAN => 'Surat Permohonan Penelitian',
            self::DISPENSASI_KULIAH => 'Surat Dispensasi Perkuliahan',
            self::DISPENSASI_MAHASISWA => 'Surat Dispensasi Mahasiswa',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::SKAK => 'SKAK',
            self::SKAK_TUNJANGAN => 'SKAK Tunjangan',
            self::PENELITIAN => 'Penelitian',
            self::DISPENSASI_KULIAH => 'Dispensasi Kuliah',
            self::DISPENSASI_MAHASISWA => 'Dispensasi Mahasiswa',
        };
    }

    /**
     * Check if letter type uses external system (Word → UB System → PDF)
     */
    public function isExternal(): bool
    {
        return match($this) {
            self::SKAK, self::SKAK_TUNJANGAN => true,
            default => false,
        };
    }

    /**
     * Get output format
     */
    public function outputFormat(): string
    {
        return $this->isExternal() ? 'word' : 'pdf';
    }

    /**
     * Get color output format
     */
    public function colorOutputFormat(): string
    {
        return $this->isExternal() ? 'info' : 'error';
    }


    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match($this) {
            self::SKAK, self::SKAK_TUNJANGAN => 'fa-file-word',
            default => 'fa-file-pdf',
        };
    }

    /**
     * Get color for badge
     */
    public function color(): string
    {
        return match($this) {
            self::SKAK => 'primary',
            self::SKAK_TUNJANGAN => 'info',
            self::PENELITIAN => 'success',
            self::DISPENSASI_KULIAH => 'warning',
            self::DISPENSASI_MAHASISWA => 'secondary',
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::SKAK => 'Surat keterangan bahwa mahasiswa masih aktif kuliah di semester berjalan',
            self::SKAK_TUNJANGAN => 'Surat keterangan aktif kuliah untuk persyaratan tunjangan orang tua/wali',
            self::PENELITIAN => 'Surat permohonan izin penelitian di instansi/perusahaan',
            self::DISPENSASI_KULIAH => 'Surat dispensasi tidak mengikuti perkuliahan karena alasan tertentu',
            self::DISPENSASI_MAHASISWA => 'Surat dispensasi untuk keperluan mahasiswa lainnya',
        };
    }
}
