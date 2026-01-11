<?php

namespace App\Models;

use App\Traits\RecordSignature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends SpatieRole
{
    use SoftDeletes, RecordSignature;

    protected $primaryKey = 'id';

//    protected $guarded = [];

    protected $fillable = [
        'code',
        'name',
        'guard_name',
        'is_editable',
        'is_deletable',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_editable' => 'boolean',
        'is_deletable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ==========================================================
    // SCOPES
    // ==========================================================

    public function scopeFilter($query, array $filters)
    {
        $query->when(!empty($filters['search']), function ($query) use ($filters) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('createdByUser', fn ($q) =>
                    $q->where('name', 'like', "%{$search}%")
                    );
            });
        });
    }

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ==========================================================
    // SIGNATURE ACCESSORS
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

    protected function deletedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->deleted_at
                ? Carbon::parse($this->deleted_at)->translatedFormat('d F Y H:i:s')
                : null
        );
    }

    protected function createdByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->created_by
                ? ($this->createdByUser?->name ?? "Administrator")
                : "Administrator"
        );
    }

    protected function updatedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->updated_by
                ? ($this->updatedByUser?->name ?? "Administrator")
                : "Administrator"
        );
    }

    protected function deletedByName(): Attribute
    {
        return Attribute::make(
            get: fn () =>
            $this->deleted_by
                ? ($this->deletedByUser?->name ?? "Administrator")
                : "Administrator"
        );
    }
}
