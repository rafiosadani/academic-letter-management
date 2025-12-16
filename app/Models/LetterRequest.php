<?php

namespace App\Models;

use App\Enums\LetterType;
use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetterRequest extends Model
{
    use SoftDeletes, RecordSignature;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'letter_type',
        'student_id',
        'semester_id',
        'academic_year_id',
        'data_input',
        'status',
        'external_system_status',
        'rejected_reason',
        'is_editable',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_input' => 'array',
        'is_editable' => 'boolean',
        'letter_type' => LetterType::class,
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function student(): belongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function semester(): belongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function academicYear(): belongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function rejectionHistories(): HasMany
    {
        return $this->hasMany(RejectionHistory::class);
    }

    public function letterCancellations(): HasMany
    {
        return $this->hasMany(LetterCancellation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeOfType($query, LetterType $type)
    {
        return $query->where('letter_type', $type);
    }

    public function scopeWithStats($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['in_progress', 'external_processing', 'resubmitted']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // ============================================
    // BUSINESS METHODS
    // ============================================

    public function currentApproval(): ?Approval
    {
        return $this->approvals()->where('is_active', true)->first();
    }

    public function requiresExternalSystem(): bool
    {
        return $this->letter_type->isExternal();
    }

    public function canBeEditedByStudent(): bool
    {
        return $this->is_editable
            && in_array($this->status, ['in_progress', 'rejected']);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['completed', 'cancelled']);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'Sedang Diproses',
            'external_processing' => 'Proses di Sistem Pusat',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'resubmitted' => 'Diajukan Ulang',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'info',
            'external_processing' => 'warning',
            'approved' => 'success',
            'rejected' => 'error',
            'resubmitted' => 'warning',
            'cancelled' => 'secondary',
            'completed' => 'success',
            default => 'secondary',
        };
    }
}
