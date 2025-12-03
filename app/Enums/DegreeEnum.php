<?php

namespace App\Enums;

enum DegreeEnum: string
{
    case D3 = 'D3';
    case D4 = 'D4';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';

    /**
     * Get the display label for the degree
     */
    public function getLabel(): string
    {
        return match($this) {
            self::D3 => 'D3 - Diploma 3',
            self::D4 => 'D4 - Diploma 4',
            self::S1 => 'S1 - Sarjana',
            self::S2 => 'S2 - Magister',
            self::S3 => 'S3 - Doktor',
        };
    }

    /**
     * Get short label (without description)
     */
    public function getShortLabel(): string
    {
        return match($this) {
            self::D3 => 'Diploma 3',
            self::D4 => 'Diploma 4',
            self::S1 => 'Sarjana',
            self::S2 => 'Magister',
            self::S3 => 'Doktor',
        };
    }

    /**
     * Get badge color class for UI
     */
    public function getBadgeColor(): string
    {
        return match($this) {
            self::D3 => 'bg-info/10 text-info border border-info/20',
            self::D4 => 'bg-primary/10 text-primary border border-primary/20',
            self::S1 => 'bg-success/10 text-success border border-success/20',
            self::S2 => 'bg-warning/10 text-warning border border-warning/20',
            self::S3 => 'bg-error/10 text-error border border-error/20',
        };
    }

    /**
     * Get all degrees as array for select dropdown
     * Format: ['D3' => 'D3 - Diploma 3', ...]
     */
    public static function toSelectArray(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }

    /**
     * Get all values as array (for validation)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Try to get enum from string value
     */
    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}
