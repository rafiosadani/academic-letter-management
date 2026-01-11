<?php

namespace App\Enums;

enum NotificationPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Rendah',
            self::NORMAL => 'Normal',
            self::HIGH => 'Tinggi',
            self::URGENT => 'Mendesak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW => 'bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100',
            self::NORMAL => 'bg-info/10 text-info border border-info/20',
            self::HIGH => 'bg-warning/10 text-warning border border-warning/20',
            self::URGENT => 'bg-error/10 text-error border border-error/20',
        };
    }
}
