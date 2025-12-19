<?php

namespace App\Models;

use App\Traits\RecordSignature;
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
        'actual_approver_id',
        'status',
        'note',
        'content_edited',
        'edited_fields',
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
        'edited_fields' => 'array',
        'content_edited' => 'boolean',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function letterRequest(): belongsTo
    {
        return $this->belongsTo(LetterRequest::class);
    }

    public function assignedApprover(): belongsTo
    {
        return $this->belongsTo(User::class, 'assigned_approver_id');
    }

    public function actualApprover(): belongsTo
    {
        return $this->belongsTo(User::class, 'actual_approver_id');
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
            ->orWhere('actual_approver_id', $approverId);
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

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'skipped' => 'Dilewati',
            'published' => 'Diterbitkan',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'error',
            'skipped' => 'secondary',
            'published' => 'info',
            default => 'secondary',
        };
    }
}
