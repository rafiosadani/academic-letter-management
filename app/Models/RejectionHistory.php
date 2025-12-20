<?php

namespace App\Models;

use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RejectionHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'letter_request_id',
        'approval_id',
        'step',
        'rejected_by',
        'rejection_type',
        'reason',
        'rejected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rejected_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function letterRequest(): BelongsTo
    {
        return $this->belongsTo(LetterRequest::class);
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getRejectionTypeLabelAttribute(): string
    {
        return match($this->rejection_type) {
            'data_invalid' => 'Data Tidak Valid',
            'format_error' => 'Format Salah',
            'requirement_not_met' => 'Syarat Tidak Terpenuhi',
            'policy_violation' => 'Melanggar Kebijakan',
            'incomplete_document' => 'Dokumen Tidak Lengkap',
            'other' => 'Lainnya',
            default => 'Unknown',
        };
    }

    protected function rejectedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->rejected_at
                ? $this->rejected_at->translatedFormat('d F Y')
                : null
        );
    }

    protected function rejectedAtTime(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->rejected_at
                ? $this->rejected_at->format('H:i') . ' WIB'
                : null
        );
    }

    protected function rejectedAtFull(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->rejectedAtFormatted && $this->rejectedAtTime
                ? "{$this->rejectedAtFormatted}, {$this->rejectedAtTime}"
                : null
        );
    }
}
