<?php

namespace App\Models;

use App\Enums\LetterType;
use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterNumberConfig extends Model
{
    use RecordSignature;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'letter_type',
        'prefix',
        'code',
        'padding'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'letter_type' => LetterType::class,
        'padding' => 'integer'
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Relationship to creator user
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to updater user
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get creator name (for display)
     */
    public function createdByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_by
                ? ($this->creator?->profile?->full_name ?? "Administrator")
                : "Administrator"
        );
    }

    /**
     * Get updater name (for display)
     */
    public function updatedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->updated_by
            ? ($this->updater?->profile?->full_name ?? "Administrator")
            : "Administrator"
        );
    }

    /**
     * Get formatted created_at
     */
    public function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_at
            ? Carbon::parse($this->created_at)->translatedFormat('d F Y H:i:s')
            : null
        );
    }

    /**
     * Get formatted updated_at
     */
    public function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->updated_at
            ? Carbon::parse($this->updated_at)->translatedFormat('d F Y H:i:s')
            : null
        );
    }

    // ============================================
    // BUSINESS METHODS
    // ============================================

    /**
     * Generate preview of letter number format
     */
    public function generatePreview(int $sequence = 1): string
    {
        $paddedSequence = str_pad($sequence, $this->padding, '0', STR_PAD_LEFT);
        $year = now()->year;

        return "{$paddedSequence}/{$this->prefix}/{$this->code}/{$year}";
    }

    /**
     * Get label for letter type
     */
    public function getLetterTypeLabelAttribute(): string
    {
        return $this->letter_type->label();
    }
}
