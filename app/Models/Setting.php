<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'order'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order' => 'integer',
    ];

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope to get settings by group
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group)->orderBy('order');
    }

    /**
     * Scope to get settings by type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ============================================
    // BUSINESS METHODS
    // ============================================

    /**
     * Check if setting is an image type
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Get image URL if setting is image type
     */
    public function getImageUrl(): ?string
    {
        if (!$this->isImage() || !$this->value) {
            return null;
        }

        return asset('storage/' . $this->value);
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllIsArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Get settings grouped by group
     */
    public static function getAllGrouped(): array
    {
        return static::orderBy('order')->get()->groupBy('group')->toArray();
    }
}
