<?php

namespace App\Models;

use App\Enums\LetterType;
use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'letter_number',
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

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function rejectionHistories(): HasMany
    {
        return $this->hasMany(RejectionHistory::class);
    }

    public function cancellation(): HasMany
    {
        return $this->hasMany(LetterCancellation::class);
    }

    public function currentApproval(): HasOne
    {
        return $this->hasOne(Approval::class)->where('is_active', true);
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

    public function scopeOfType($query, LetterType|string $letterType)
    {
        $type = $letterType instanceof LetterType ? $letterType->value : $letterType;
        return $query->where('letter_type', $type);
    }

    public function scopeWithStatus($query, string $status)
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

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['status'] ?? null, fn ($query, $status) =>
                $query->withStatus($status)
            )
            ->when($filters['letter_type'] ?? null, fn ($query, $letterType) =>
                $query->ofType($letterType)
            )
            ->when($filters['search'] ?? null, function ($query, $search) {
                $searchTerm = "%{$search}%";

                $query->where(function ($query) use ($searchTerm) {
                    $query->where('status', 'like', $searchTerm)
                        ->orWhereHas('academicYear', fn ($subQuery) =>
                            $subQuery->where('year_label', 'like', $searchTerm)
                        )
                        ->orWhereHas('semester', fn ($subQuery) =>
                            $subQuery->where('semester_type', 'like', $searchTerm)
                        );
                });
            });

        return $query;
    }


    // ============================================
    // BUSINESS METHODS
    // ============================================

    public function finalDocument(): ?Document
    {
        return $this->documents()
            ->where('category', 'generated')
            ->where('type', 'final')
            ->whereNotNull('hash')
            ->latest()
            ->first();
    }

    public function supportingDocuments()
    {
        return $this->documents()->where('category', 'supporting');
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

    public function hasStudentList(): bool
    {
        return isset($this->data_input['student_list']) &&
            is_array($this->data_input['student_list']) &&
            count($this->data_input['student_list']) > 0;
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

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'in_progress'          => 'fa-solid fa-spinner',
            'external_processing'  => 'fa-solid fa-arrows-rotate',
            'approved'             => 'fa-solid fa-circle-check',
            'rejected'             => 'fa-solid fa-circle-xmark',
            'resubmitted'          => 'fa-solid fa-rotate-right',
            'cancelled'            => 'fa-solid fa-ban',
            'completed'            => 'fa-solid fa-check-double',
            default                => 'fa-solid fa-circle-question',
        };
    }

    /**
     * Get formatted data_input with human-readable dates.
     *
     * Converts date fields (YYYY-MM-DD) to Indonesian format (DD Month YYYY).
     */
    public function getFormattedDataInputAttribute(): array
    {
        $rawData = $this->data_input ?? [];
        $formFields = $this->letter_type->formFields();
        $formatted = [];

        // Loop through formFields to preserve order
        foreach ($formFields as $fieldName => $config) {
            if (isset($rawData[$fieldName])) {
                $value = $rawData[$fieldName];

                if ($config['type'] === 'student_list') {
                    continue;
                }

                if (is_array($value)) {
                    continue;
                }

                // Format date fields
                if ($config['type'] === 'date' && $value) {
                    try {
                        $value = \Carbon\Carbon::parse($value)
                            ->locale('id')
                            ->translatedFormat('d F Y');
                    } catch (\Exception $e) {
                        // Keep original value if parse fails
                    }
                }

                $formatted[$fieldName] = $value;
            }
        }

        return $formatted;
    }

    public function getStudentListAttribute(): array
    {
        $studentList = $this->data_input['student_list'] ?? [];

        return is_string($studentList) ? json_decode($studentList, true) : $studentList;
    }

    // ==========================================================
    // SIGNATURE ACCESSORS
    // ==========================================================
    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_at
                ? $this->created_at->translatedFormat('d F Y')
                : null
        );
    }

    protected function createdAtTime(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_at
                ? $this->created_at->format('H:i') . ' WIB'
                : null
        );
    }

    protected function createdAtFull(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->createdAtFormatted && $this->createdAtTime
                ? "{$this->createdAtFormatted}, {$this->createdAtTime}"
                : null
        );
    }
}
