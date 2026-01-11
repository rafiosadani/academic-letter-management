<?php

namespace App\Models;

use App\Enums\ApprovalAction;
use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlow extends Model
{
    use RecordSignature;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'letter_type',
        'step',
        'step_label',
        'required_positions',
        'can_edit_content',
        'is_editable',
        'on_reject',
        'is_final',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'letter_type' => LetterType::class,
        'required_positions' => 'array',
        'on_reject' => ApprovalAction::class,
        'can_edit_content' => 'boolean',
        'is_editable' => 'boolean',
        'is_final' => 'boolean',
        'step' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get current pejabat for this approval step
     * Returns first available pejabat if multiple positions
     */
    public function currentPejabat()
    {
        foreach ($this->required_positions as $position) {
            $pejabat = FacultyOfficial::getCurrentPejabat($position);
            if ($pejabat) {
                return $pejabat;
            }
        }
        return null;
    }

    /**
     * Get all current pejabat for this approval step
     */
    public function allCurrentPejabat()
    {
        $pejabats = [];
        foreach ($this->required_positions as $position) {
            $pejabat = FacultyOfficial::getCurrentPejabat($position);
            if ($pejabat) {
                $pejabats[] = $pejabat;
            }
        }
        return collect($pejabats);
    }

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
    // SCOPES
    // ============================================

    /**
     * Scope for filtering approval flows
     */
    public function scopeFilter($query, array $filters)
    {
        // Filter by letter type
        $query->when($filters['letter_type'] ?? false, function ($query, $letterType) {
            return $query->forLetterType($letterType);
        });

        return $query;
    }

    /**
     * Scope by letter type
     */
    public function scopeForLetterType($query, LetterType|string $letterType)
    {
        $type = $letterType instanceof LetterType ? $letterType->value : $letterType;
        return $query->where('letter_type', $type);
    }

    /**
     * Scope to get ordered steps
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step', 'asc');
    }

    /**
     * Scope final steps
     */
    public function scopeFinalStep($query)
    {
        return $query->where('is_final', true);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get position labels as string (for display)
     */
    public function getPositionLabelsAttribute(): string
    {
        return collect($this->required_positions)
            ->map(fn($pos) => OfficialPosition::from($pos)->label())
            ->join(' atau ');
    }

    /**
     * Get reject action label
     */
    public function getRejectActionLabelAttribute(): string
    {
        return $this->on_reject->label();
    }

    /**
     * Check if step requires multiple positions
     */
    public function requiresMultiplePositions(): bool
    {
        return count($this->required_positions) > 1;
    }

    // ==========================================================
    // SIGNATURE ACCESSORS (CUSTOM PATTERN)
    // ==========================================================

    protected function createdByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_by
                ? ($this->creator?->profile?->full_name ?? "Administrator")
                : "Administrator"
        );
    }

    protected function updatedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->updated_by
                ? ($this->updater?->profile?->full_name ?? "Administrator")
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

    // ============================================
    // STATIC METHODS
    // ============================================

    /**
     * Get grouped flows by letter type
     */
    public static function getGroupedFlows(array $filters = []): array
    {
        $allFlows = static::filter($filters)
            ->orderBy('letter_type')
            ->orderBy('step')
            ->get();

        $groupedFlows = [];
        $letterTypeFilter = $filters['letter_type'] ?? null;

        foreach (LetterType::cases() as $letterType) {
            $flows = $allFlows->where('letter_type', $letterType->value);

            // Skip if no flows
            if ($flows->isEmpty()) {
                continue;
            }

            $groupedFlows[] = [
                'letter_type' => $letterType,
                'total_steps' => $flows->count(),
                'is_complete' => $flows->where('is_final', true)->isNotEmpty(),
                'flows' => $flows,
                'first_flow' => $flows->first(),
            ];
        }

        return $groupedFlows;
    }

    /**
     * Get flow for specific letter type
     */
    public static function getFlowForLetter(LetterType|string $letterType): \Illuminate\Database\Eloquent\Collection
    {
        return static::forLetterType($letterType)->ordered()->get();
    }

    /**
     * Get next step number for letter type
     */
    public static function getNextStepNumber(LetterType|string $letterType): int
    {
        $type = $letterType instanceof LetterType ? $letterType->value : $letterType;

        $maxStep = static::where('letter_type', $type)->max('step');

        return $maxStep ? $maxStep + 1 : 1;
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Reorder steps after delete
     */
    public static function reorderSteps(LetterType|string $letterType): void
    {
        $type = $letterType instanceof LetterType ? $letterType->value : $letterType;

        $flows = static::where('letter_type', $type)
            ->orderBy('step', 'asc')
            ->get();

        foreach ($flows as $index => $flow) {
            $flow->update(['step' => $index + 1]);
        }
    }
}
