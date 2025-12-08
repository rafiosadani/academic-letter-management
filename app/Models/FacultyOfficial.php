<?php

namespace App\Models;

use App\Enums\OfficialPosition;
use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacultyOfficial extends Model
{
    use SoftDeletes, RecordSignature;

    protected $fillable = [
        'user_id',
        'position',
        'study_program_id',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'position' => OfficialPosition::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['is_active'];

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for active assignments only
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', now()->toDateString());
        });
    }

    /**
     * Scope for ended assignments
     */
    public function scopeEnded($query)
    {
        return $query->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString());
    }

    /**
     * Scope for specific position
     */
    public function scopePosition($query, OfficialPosition|string $position)
    {
        $positionValue = $position instanceof OfficialPosition ? $position->value : $position;
        return $query->where('position', $positionValue);
    }

    /**
     * Scope for assignments at specific date
     */
    public function scopeAtDate($query, string $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where(function($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $date);
            });
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(!empty($filters['search']), function ($query) use ($filters) {
            $search = $filters['search'];
            $searchTerm = "%{$search}%";

            $query->where(function ($query) use ($search, $searchTerm) {
                $query->where('code', 'like', $searchTerm)
                    ->orWhere('name', 'like', $searchTerm)
                    ->orWhere('degree', 'like', $searchTerm);

                $query->orWhereHas('createdByUser.profile', fn ($subQuery) =>
                $subQuery->where('full_name', 'like', $searchTerm)
                );
            });
        });
    }

    // ============================================
    // ACCESSORS (MUTATORS)
    // ============================================

    /**
     * Check if assignment is currently active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->end_date === null || $this->end_date->isFuture() || $this->end_date->isToday();
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->is_active) {
            true => '<span class="badge bg-success/10 text-success"><i class="fa-solid fa-circle-check mr-1"></i>Aktif</span>',
            false => '<span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100 text-tinybadge"><i class="fa-solid fa-circle-xmark mr-1"></i>Berakhir</span>',
        };
    }

    /**
     * Get formatted period
     */
    public function getPeriodAttribute(): string
    {
        $start = $this->start_date->translatedFormat('d F Y');
        $end = $this->end_date ? $this->end_date->translatedFormat('d F Y') : 'Sekarang';

        return "{$start} - {$end}";
    }

    /**
     * Get position label
     */
    public function getPositionLabelAttribute(): string
    {
        return $this->position->label();
    }

    /**
     * Get short position label
     */
    public function getPositionShortLabelAttribute(): string
    {
        return $this->position->shortLabel();
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ==========================================================
    // SIGNATURE ACCESSORS
    // ==========================================================

    protected function createdByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_by
                ? ($this->createdByUser?->profile?->full_name ?? "Administrator")
                : "Administrator"
        );
    }

    protected function updatedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->updated_by
                ? ($this->updatedByUser?->profile?->full_name ?? "Administrator")
                : "Administrator"
        );
    }

    protected function deletedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->deleted_by
                ? ($this->deletedByUser?->profile?->full_name ?? "Administrator")
                : "Administrator"
        );
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_at
                ? Carbon::parse($this->created_at)->translatedFormat('d F Y H:i:s')
                : null
        );
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->updated_at
                ? Carbon::parse($this->updated_at)->translatedFormat('d F Y H:i:s')
                : null
        );
    }

    protected function deletedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->deleted_at
                ? Carbon::parse($this->deleted_at)->translatedFormat('d F Y H:i:s')
                : null
        );
    }

    // ============================================
    // STATIC METHODS
    // ============================================

    /**
     * Get pejabat for specific position at specific date
     *
     * @param OfficialPosition|string $position
     * @param string $date (Y-m-d format)
     * @return FacultyOfficial|null
     */
    public static function getPejabatAt(OfficialPosition|string $position, string $date): ?self
    {
        $positionValue = $position instanceof OfficialPosition ? $position->value : $position;

        return static::where('position', $positionValue)
            ->atDate($date)
            ->with(['user.profile', 'studyProgram'])
            ->first();
    }

    /**
     * Get current pejabat for specific position
     *
     * @param OfficialPosition|string $position
     * @return FacultyOfficial|null
     */
    public static function getCurrentPejabat(OfficialPosition|string $position): ?self
    {
        return static::getPejabatAt($position, now()->toDateString());
    }

    /**
     * Check if user has active assignment for position
     *
     * @param int $userId
     * @param OfficialPosition|string $position
     * @return bool
     */
    public static function hasActiveAssignment(int $userId, OfficialPosition|string $position): bool
    {
        $positionValue = $position instanceof OfficialPosition ? $position->value : $position;

        return static::where('user_id', $userId)
            ->where('position', $positionValue)
            ->active()
            ->exists();
    }

    /**
     * Check if there's period overlap for user+position
     *
     * @param int $userId
     * @param OfficialPosition|string $position
     * @param string $startDate
     * @param string|null $endDate
     * @param int|null $excludeId (for update scenario)
     * @return bool
     */
    public static function hasOverlap(
        int $userId,
        OfficialPosition|string $position,
        string $startDate,
        ?string $endDate = null,
        ?int $excludeId = null
    ): bool {
        $positionValue = $position instanceof OfficialPosition ? $position->value : $position;

        $query = static::where('user_id', $userId)
            ->where('position', $positionValue);

        // Exclude current record for update
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Check overlap logic
        $query->where(function($q) use ($startDate, $endDate) {
            if ($endDate) {
                // New period has end date
                $q->where(function($sq) use ($startDate, $endDate) {
                    // Existing period overlaps with new period
                    $sq->where(function($ssq) use ($startDate) {
                        $ssq->whereNull('end_date')
                            ->orWhere('end_date', '>=', $startDate);
                    })->where('start_date', '<=', $endDate);
                });
            } else {
                // New period is open-ended (end_date = null)
                $q->where(function($sq) use ($startDate) {
                    $sq->whereNull('end_date')
                        ->orWhere('end_date', '>=', $startDate);
                });
            }
        });

        return $query->exists();
    }

    public static function hasActiveForPosition(
        OfficialPosition|string $position,
        ?int $studyProgramId = null,
        ?int $excludeId = null
    ): bool {
        $positionValue = $position instanceof OfficialPosition ? $position->value : $position;

        $query = static::where('position', $positionValue)->active();

        // For Kaprodi, check per study program
        if ($positionValue === OfficialPosition::KETUA_PROGRAM_STUDI->value && $studyProgramId) {
            $query->where('study_program_id', $studyProgramId);
        }

        // Exclude current record for update
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Mark assignment as ended
     */
    public function markAsEnded(string $endDate): bool
    {
        return $this->update(['end_date' => $endDate]);
    }

    /**
     * Check if assignment is for Kaprodi
     */
    public function isKaprodi(): bool
    {
        return $this->position === OfficialPosition::KETUA_PROGRAM_STUDI;
    }

    /**
     * Get display name (User + Position)
     */
    public function getDisplayName(): string
    {
        $userName = $this->user->profile->full_name ?? $this->user->email;
        $positionLabel = $this->position->label();

        if ($this->isKaprodi() && $this->studyProgram) {
            return "{$userName} - {$positionLabel} {$this->studyProgram->degree_name}";
        }

        return "{$userName} - {$positionLabel}";
    }
}
