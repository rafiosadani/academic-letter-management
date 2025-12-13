<?php

namespace App\Services;

use App\Enums\LetterType;
use App\Models\LetterCounter;
use App\Models\LetterNumberConfig;
use Illuminate\Support\Facades\DB;

class LetterNumberService
{
    /**
     * Generate letter number for a given letter type
     */
    public function generate(LetterType $letterType): string
    {
        // Only generate for PDF types (internal system)
        if (!$letterType->needsAutoNumbering()) {
            throw new \Exception("Letter type {$letterType->value} does not need auto numbering (external system).");
        }

        return DB::transaction(function () use ($letterType) {
            // get configuration
            $config = LetterNumberConfig::where('letter_type', $letterType->value)->firstOrFail();

            // get or create counter for this type + year
            $counter = LetterCounter::firstOrCreate(
                [
                    'letter_type' => $letterType->value,
                    'year' => now()->year,
                ],
                ['last_sequence' => 0]
            );

            // lock row and increment sequence
            $counter->lockForUpdate();
            $newSequence = $counter->incrementAndGet();

            // build letter number
            $paddedSequence = str_pad(
                $newSequence,
                $config->padding,
                "0",
                STR_PAD_LEFT
            );

            $letterNumber = sprintf(
                '%s/%s/%s/%s',
                $paddedSequence,
                $config->prefix,
                $config->code,
                now()->year
            );

            return $letterNumber;
        });
    }

    /**
     * Get current sequence for a letter type in current year
     */
    public function getCurrentSequence(LetterType $letterType): int
    {
        $counter = LetterCounter::where('letter_type', $letterType->value)
            ->where('year', now()->year)
            ->first();

        return $counter?->last_sequence ?? 0;
    }

    /**
     * Get preview of letter number format
     * @return string Preview (e.g., "001/UN10.F1601/LL/2025")
     */
    public function getPreview(LetterType $letterType, int $sequence = 1): string
    {
        $config = LetterNumberConfig::where('letter_type', $letterType->value)->first();

        if (!$config) {
            return 'Configuration not found';
        }

        return $config->generatePreview($sequence);
    }

    /**
     * Reset counter for a specific letter type and year
     * Use with caution - this will reset the sequence
     */
    public function resetCounter(LetterType $letterType, int $year): bool
    {
        return LetterCounter::where('letter_type', $letterType->value)
            ->where('year', $year)
            ->update(['last_sequence' => 0]) > 0;
    }
}