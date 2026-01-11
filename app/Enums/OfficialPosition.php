<?php

namespace App\Enums;

enum OfficialPosition: string
{
    case DEKAN = 'dekan';
    case WAKIL_DEKAN_AKADEMIK = 'wakil_dekan_akademik';

    case KETUA_PROGRAM_STUDI = 'ketua_program_studi';

    case KASUBBAG_AKADEMIK = 'kasubbag_akademik';

    case STAF_AKADEMIK = 'staf_akademik';

    /**
     * Get label untuk display
     */
    public function label(): string
    {
        return match($this) {
            self::DEKAN => 'Dekan',
            self::WAKIL_DEKAN_AKADEMIK => 'Wakil Dekan Bidang Akademik',
            self::KETUA_PROGRAM_STUDI => 'Ketua Program Studi',
            self::KASUBBAG_AKADEMIK => 'Kepala Subbagian Akademik',
            self::STAF_AKADEMIK => 'Staf Akademik',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::DEKAN => 'Dekan',
            self::WAKIL_DEKAN_AKADEMIK => 'WD Bidang Akademik',
            self::KETUA_PROGRAM_STUDI => 'Kaprodi',
            self::KASUBBAG_AKADEMIK => 'Kasubbag Akademik',
            self::STAF_AKADEMIK => 'Staf Akademik',
        };
    }

    /**
     * Check if position requires study program
     */
    public function requiresStudyProgram(): bool
    {
        return match($this) {
            self::KETUA_PROGRAM_STUDI => true,
            default => false,
        };
    }

    /**
     * Check if position must be unique (only 1 active at a time globally)
     * Kaprodi is handled separately (unique per study program)
     */
    public function isUnique(): bool
    {
        return match($this) {
            self::DEKAN => true,
            self::WAKIL_DEKAN_AKADEMIK => true,
            self::KETUA_PROGRAM_STUDI => true,
            self::KASUBBAG_AKADEMIK => true,
            default => false,
        };
    }

    public function isDynamic(): bool
    {
        return $this === self::KETUA_PROGRAM_STUDI;
    }

    /**
     * Get hierarchical level (for sorting/display)
     */
    public function level(): int
    {
        return match($this) {
            self::DEKAN => 1,
            self::WAKIL_DEKAN_AKADEMIK => 2,
            self::KETUA_PROGRAM_STUDI => 3,
            self::KASUBBAG_AKADEMIK => 4,
            self::STAF_AKADEMIK => 5,
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match($this) {
            self::DEKAN => 'fa-crown',
            self::WAKIL_DEKAN_AKADEMIK => 'fa-user-graduate',
            self::KETUA_PROGRAM_STUDI => 'fa-user-tie',
            self::KASUBBAG_AKADEMIK => 'fa-graduation-cap',
            self::STAF_AKADEMIK => 'fa-briefcase',
        };
    }

    /**
     * Get color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::DEKAN => 'primary',
            self::WAKIL_DEKAN_AKADEMIK => 'info',
            self::KETUA_PROGRAM_STUDI => 'success',
            self::KASUBBAG_AKADEMIK => 'secondary',
            self::STAF_AKADEMIK => 'warning',
        };
    }
}
