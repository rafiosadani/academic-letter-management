<?php

namespace App\Models;

use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use SoftDeletes, RecordSignature;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'letter_request_id',
        'uploaded_by',
        'category',
        'type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function letterRequest(): BelongsTo
    {
        return $this->belongsTo(LetterRequest::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeSupporting($query)
    {
        return $query->where('category', 'supporting');
    }

    public function scopeGenerated($query)
    {
        return $query->where('category', 'generated');
    }

    public function scopeExternal($query)
    {
        return $query->where('category', 'external');
    }

    public function scopeFinal($query)
    {
        return $query->where('type', 'final');
    }

    public function scopeDraft($query)
    {
        return $query->where('type', 'draft');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getFileSizeFormattedAttribute(): string
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getDownloadNameAttribute(): string
    {
        $letter = $this->letterRequest;

        // For supporting documents, use original name
        if ($this->category === 'supporting') {
            return $this->file_name;
        }

        // For generated/external documents, use custom format
        $name = sprintf(
            '%s - %s - %s.%s',
            $letter->letter_number ?? 'Draft',
            $letter->letter_type->shortLabel() ?? 'Surat',
            $letter->student->profile->full_name ?? 'Unknown',
            $this->extension
        );

        return $name;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'supporting' => 'Dokumen Pendukung',
            'generated' => 'Surat Generated',
            'external' => 'Surat External',
            default => 'Unknown',
        };
    }

    public function getTypeLabelAttribute(): ?string
    {
        if (!$this->type) {
            return null;
        }

        return match($this->type) {
            'draft' => 'Draft',
            'final' => 'Final',
            default => 'Unknown',
        };
    }

    public function fileExists(): bool
    {
        return Storage::exists($this->file_path);
    }

    public function getIconAttribute(): string
    {
        return match(true) {
            str_contains($this->mime_type, 'pdf') => 'fa-file-pdf text-error',
            str_contains($this->mime_type, 'word') => 'fa-file-word text-info',
            str_contains($this->mime_type, 'image') => 'fa-file-image text-success',
            default => 'fa-file text-slate-400',
        };
    }
}
