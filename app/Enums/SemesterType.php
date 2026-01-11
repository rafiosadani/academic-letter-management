<?php

namespace App\Enums;

enum SemesterType: string
{
    CASE GANJIL = 'Ganjil';
    CASE GENAP = 'Genap';

    public function label(): string
    {
        return $this->value;
    }

    public function shortCode(): string
    {
        return match($this) {
            self::GANJIL => 'GJL',
            self::GENAP => 'GNP',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::GANJIL => 'bg-primary/10 text-primary border border-primary/20 dark:bg-accent/15 dark:text-accent-light',
            self::GENAP => 'bg-success/10 text-success border border-success/20',
        };
    }
}
