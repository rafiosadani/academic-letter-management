<?php

namespace App\Models;

use App\Enums\DegreeEnum;
use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class StudyProgram extends Model
{
    use SoftDeletes, RecordSignature;
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'name',
        'degree',
    ];

    protected $casts = [
        'degree' => DegreeEnum::class,
    ];

    // ==========================================================
    // SCOPES
    // ==========================================================

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

    // ==========================================================
    // ACCESSORS (MUTATORS)
    // ==========================================================

    /**
     * Get the degree name (formatted: "S1 Teknik Informatika")
     */
    public function getDegreeNameAttribute(): string
    {
        if (!$this->degree) {
            return $this->name;
        }

        return $this->degree->value . ' ' . $this->name;
    }

    /**
     * Get the full degree name with description (formatted: "S1 - Sarjana Teknik Informatika")
     */
    public function getFullDegreeNameAttribute(): string
    {
        if (!$this->degree) {
            return $this->name;
        }

        return $this->degree->getShortLabel() . ' ' . $this->name;
    }

    /**
     * Get the degree label (e.g., "S1 - Sarjana")
     */
    public function getDegreeLabelAttribute(): ?string
    {
        return $this->degree?->getLabel();
    }

    /**
     * Get the degree badge color
     */
    public function getDegreeBadgeColorAttribute(): ?string
    {
        return $this->degree?->getBadgeColor();
    }

    public static function getFormattedNames(): array
    {
        return self::query()
        ->withoutTrashed()
            ->select('id', 'degree', 'name')
            ->get()
            ->pluck('degree_name')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
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
}
