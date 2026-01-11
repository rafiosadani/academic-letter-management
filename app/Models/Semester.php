<?php

namespace App\Models;

use App\Enums\SemesterType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'academic_year_id',
        'semester_type',
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
            'semester_type' => SemesterType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ==========================================================
    // SCOPES
    // ==========================================================

    public function scopeFilter($query, array $filters)
    {
        $query->when(!empty($filters['search']), function ($query) use ($filters) {
            $search = $filters['search'];
            $searchTerm = "%{$search}%";

            $query->where(function ($query) use ($searchTerm) {
                $query->where('code', 'like', $searchTerm)
                    ->orWhere('semester_type', 'like', $searchTerm);

                $query->orWhereHas('academicYear', fn ($subQuery) =>
                    $subQuery->where('year_label', 'like', $searchTerm)
                );
            });
        });
    }

    // ==========================================================
    // ACCESSORS (MUTATORS)
    // ==========================================================

    public function getStatusBadgeAttribute()
    {
        return match($this->is_active) {
            true => '<span class="badge bg-success/10 text-success"><i class="fa-solid fa-circle-check mr-1"></i>Aktif</span>',
            false => '<span class="badge bg-error/10 text-error"><i class="fa-solid fa-circle-minus mr-1"></i>Nonaktif</span>',
            default => '<span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100"><i class="fa-solid fa-question-circle mr-1"></i>Status Tidak Diketahui</span>',
        };
    }

    public function getSemesterBadgeAttribute()
    {
        $badgeClass = $this->semester_type->badgeClass();
        $label = $this->semester_type->label();

        return "<span class=\"badge {$badgeClass}\">{$label}</span>";
    }

    public function getFullLabelAttribute()
    {
        return $this->academicYear->year_label . ' - ' . $this->semester_type->label();
    }

    public function getPeriodTextAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return '-';
        }

        return $this->start_date->translatedFormat('d M Y') . ' - ' . $this->end_date->translatedFormat('d M Y');
    }

    public function getRelativePeriodStatusAttribute()
    {
        if ($this->is_active) {
            return 'current';
        }

        $activeSemester = self::where('is_active', true)->first();

        if (!$activeSemester || !$activeSemester->start_date || !$this->start_date) {
            return 'unknown';
        }

        if ($this->start_date < $activeSemester->start_date) {
            return 'past';
        }

        return 'future';
    }

    public function getActivationButtonIconAttribute()
    {
        return match($this->relative_period_status) {
            'current' => 'fa-solid fa-circle-check',
            'past' => 'fa-solid fa-clock-rotate-left',
            'future' => 'fa-solid fa-calendar-plus',
            default => 'fa-solid fa-circle-question',
        };
    }

    public function getActivationButtonColorAttribute()
    {
        return match($this->relative_period_status) {
            'current' => 'text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25',
            'past' => 'text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25',
            'future' => 'text-primary hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25',
            default => 'text-slate-600 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25',
        };
    }

    public function getActivationButtonTitleAttribute()
    {
        return match($this->relative_period_status) {
            'current' => 'Aktifkan Semester (Sedang Berlangsung)',
            'past' => 'Aktifkan Semester (Sudah Selesai)',
            'future' => 'Aktifkan Semester (Akan Datang)',
            default => 'Aktifkan Semester (Periode Belum Ditentukan)',
        };
    }

    public function getActivationTextAttribute()
    {
        return match($this->relative_period_status) {
            'current' => '(Sedang Berlangsung)',
            'past' => '(Sudah Selesai)',
            'future' => '(Akan Datang)',
            default => '(Periode Belum Ditentukan)',
        };
    }


    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ==========================================================
    // FORMATTED ATTRIBUTES
    // ==========================================================

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
