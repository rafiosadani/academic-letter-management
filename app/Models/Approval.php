<?php

namespace App\Models;

use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
    use SoftDeletes, RecordSignature;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'letter_request_id',
        'step',
        'step_label',
        'required_positions',
        'assigned_approver_id',
        'flow_snapshot',
        'approved_by',
        'status',
        'note',
        'approved_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'required_positions' => 'array',
        'flow_snapshot' => 'array',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function letterRequest(): BelongsTo
    {
        return $this->belongsTo(LetterRequest::class);
    }

    public function assignedApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_approver_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForApprover($query, int $approverId)
    {
        return $query->where('assigned_approver_id', $approverId)
            ->orWhere('approved_by', $approverId);
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['letter_type'] ?? null, function ($query, $letterType) {
                $query->whereHas('letterRequest', fn($q) => $q->where('letter_type', $letterType));
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                $searchTerm = "%{$search}%";

                $query->where(function ($query) use ($searchTerm) {
                    $query->where('step_label', 'like', $searchTerm)
                        ->orWhere('status', 'like', $searchTerm)

                        ->orWhereHas('letterRequest.student.profile', fn ($subQuery) =>
                            $subQuery->where('full_name', 'like', $searchTerm)
                                ->orWhere('student_or_employee_id', 'like', $searchTerm)
                        )
                        ->orWhereHas('letterRequest.academicYear', fn ($subQuery) =>
                            $subQuery->where('year_label', 'like', $searchTerm)
                        )
                        ->orWhereHas('letterRequest.semester', fn ($subQuery) =>
                            $subQuery->where('semester_type', 'like', $searchTerm)
                        );
                });
            });

        return $query;
    }

    public function getCanEditContentAttribute(): bool
    {
        return $this->flow_snapshot['can_edit_content'] ?? false;
    }

    public function getIsFinalAttribute(): bool
    {
        return $this->flow_snapshot['is_final'] ?? false;
    }

    public function getOnRejectAttribute(): ?\App\Enums\ApprovalAction
    {
        $value = $this->flow_snapshot['on_reject'] ?? null;
        return $value ? \App\Enums\ApprovalAction::from($value) : null;
    }

    // ============================================
    // HELPER
    // ============================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getStatusConfig(): array
    {
        return match ($this->status) {
            'pending' => [
                'badge' => 'warning',
                'icon'  => 'fa-solid fa-clock',
                'label' => 'Menunggu',
            ],
            'approved' => [
                'badge' => 'success',
                'icon'  => 'fa-solid fa-circle-check',
                'label' => 'Disetujui',
            ],
            'rejected' => [
                'badge' => 'error',
                'icon'  => 'fa-solid fa-circle-xmark',
                'label' => 'Ditolak',
            ],
            'skipped' => [
                'badge' => 'secondary',
                'icon'  => 'fa-solid fa-forward-step',
                'label' => 'Dilewati',
            ],
            'published' => [
                'badge' => 'info',
                'icon'  => 'fa-solid fa-bullhorn',
                'label' => 'Dipublikasikan',
            ],
            default => [
                'badge' => 'secondary',
                'icon'  => 'fa-solid fa-circle-question',
                'label' => 'Tidak Diketahui',
            ],
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->getStatusConfig()['badge'];
    }

    public function getStatusIconAttribute(): string
    {
        return $this->getStatusConfig()['icon'];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusConfig()['label'];
    }

    protected function approvedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->approved_at
                ? $this->approved_at->translatedFormat('d F Y')
                : null
        );
    }

    protected function approvedAtTime(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->approved_at
                ? $this->approved_at->format('H:i') . ' WIB'
                : null
        );
    }

    protected function approvedAtFull(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->approvedAtFormatted && $this->approvedAtTime
                ? "{$this->approvedAtFormatted}, {$this->approvedAtTime}"
                : null
        );
    }
}
