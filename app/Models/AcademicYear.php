<?php

namespace App\Models;

use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use RecordSignature;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'year_label',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ==========================================================
    // SCOPES
    // ==========================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(!empty($filters['search']), function ($query) use ($filters) {
            $search = $filters['search'];
            $searchTerm = "%{$search}%";

            $query->where(function ($query) use ($searchTerm) {
                $query->where('code', 'like', $searchTerm)
                    ->orWhere('year_label', 'like', $searchTerm);

                $query->orWhereHas('createdByUser.profile', fn ($subQuery) =>
                    $subQuery->where('full_name', 'like', $searchTerm)
                );
            });
        });
    }

    // ==========================================================
    // ACCESSORS (MUTATORS)
    // ==========================================================

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->is_active) {
            true => '<span class="badge bg-success/10 text-success"><i class="fa-solid fa-circle-check mr-1"></i>Aktif</span>',
            false => '<span class="badge bg-error/10 text-error"><i class="fa-solid fa-circle-minus mr-1"></i>Nonaktif</span>',
            default => '<span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100"><i class="fa-solid fa-question-circle mr-1"></i>Status Tidak Diketahui</span>',
        };
    }

    public function getPeriodTextAttribute()
    {
        return $this->start_date->translatedFormat('d M Y') . ' - ' . $this->end_date->translatedFormat('d M Y');
    }

    public function getActiveSemesterAttribute()
    {
        return $this->semesters()->where('is_active', 1)->first();
    }

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
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

    protected function deletedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->deleted_by
                ? ($this->deletedByUser?->profile?->full_name ?? "Administrator")
                : "Administrator"
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
