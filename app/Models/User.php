<?php

namespace App\Models;

use App\Models\UserProfile;
use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, RecordSignature;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    // ==========================================================
    // SCOPES
    // ==========================================================

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(!empty($filters['search']), function ($query) use ($filters) {
            $search = $filters['search'];
            $searchTerm = "%{$search}%";

            $query->where(function ($query) use ($search, $searchTerm) {

                $query->where('code', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);

                $query->orWhereHas('profile', fn ($subQuery) =>
                    $subQuery->where('full_name', 'like', $searchTerm)
                        ->orWhere('student_or_employee_id', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm)
                        ->orWhere('address', 'like', $searchTerm)
                );

                $query->orWhereHas('profile.studyProgram', fn ($subQuery) =>
                    $subQuery->where('name', 'like', $searchTerm)
                );

                $query->orWhereHas('createdByUser.profile', fn ($subQuery) =>
                    $subQuery->where('full_name', 'like', $searchTerm)
                );

                $query->orWhereHas('roles', fn ($subQuery) =>
                    $subQuery->where('name', 'like', $searchTerm)
                );
            });
        });
    }

    // ==========================================================
    // ACCESSORS (MUTATORS)
    // ==========================================================

    public function getStatusTextAttribute()
    {
        return $this->status ? 'Aktif' : 'Nonaktif';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            true => '<span class="badge bg-success/10 text-success"><i class="fa-solid fa-circle-check mr-1"></i>Aktif</span>',
            false => '<span class="badge bg-error/10 text-error"><i class="fa-solid fa-circle-xmark mr-1"></i>Nonaktif</span>',
            default => '<span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100"><i class="fa-solid fa-question-circle mr-1"></i>Status Tidak Diketahui</span>',
        };
    }

    public function getRoleBadgeAttribute()
    {
        $roleData = $this->getRoleColorClasses();
        $roleName = $roleData['name'] ?? 'Tidak Ada Role';
        $iconClass = $roleData['icon'] ?? 'fa-solid fa-user-tag';
        $classes = $this->getRoleBadgeClassAttribute();

        $badgeClass = "badge {$classes}";

        return "<span class=\"{$badgeClass}\"><i class=\"{$iconClass} mr-1\"></i>{$roleName}</span>";
    }

    public function getRoleBadgeClassAttribute(): string
    {
        $roleData = $this->getRoleColorClasses();

        $defaultClasses = 'bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100';

        if ($roleData) {
            $colorClasses = $roleData['classes'];

            $darkColorClasses = str_contains($colorClasses, 'primary')
                ? str_replace(['bg-primary/10', 'text-primary', 'border-primary'], ['dark:bg-accent/15', 'dark:text-accent-light', 'dark:border-accent-light'], $colorClasses)
                : $colorClasses;

            return "{$colorClasses} {$darkColorClasses}";
        }

        return $defaultClasses;
    }

    protected function getRoleColorClasses(): ?array
    {
        $roleMap = [
            'administrator' => [
                'classes' => 'bg-error/10 text-error',
                'icon' => 'fa-solid fa-user-shield',
            ],
            'staf akademik' => [
                'classes' => 'bg-info/10 text-info',
                'icon' => 'fa-solid fa-chalkboard-user',
            ],
            'kepala subbagian akademik' => [
                'classes' => 'bg-warning/10 text-warning',
                'icon' => 'fa-solid fa-user-tie',
            ],
            'mahasiswa' => [
                'classes' => 'bg-primary/10 text-primary',
                'icon' => 'fa-solid fa-user',
            ],
        ];

        $role = $this->roles->first();

        if (!$role) {
            return null;
        }

        $roleName = strtolower($role->name);

        $data = $roleMap[$roleName] ?? [
            'classes' => 'bg-success/10 text-success border border-success',
            'icon' => 'fa-solid fa-check',
        ];

        // Tambahkan nama role yang sebenarnya
        $data['name'] = $role->name;

        return $data;
    }

    public function getRoleAttribute()
    {
        return $this->roles->first();
    }

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
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
