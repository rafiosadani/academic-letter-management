<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait RecordSignature
{
    protected static function bootRecordSignature()
    {
        static::creating(function ($model) {
            $user = Auth::user();

            $model->created_by = $user?->id;
            $model->updated_by = $user?->id;
        });

        static::updating(function ($model) {
            $user = Auth::user();

            $model->updated_by = $user?->id;
        });

        if (self::usesSoftDeletes()) {
            static::deleting(function ($model) {
                if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                    // Force delete, tidak perlu set deleted_by
                    return;
                }

                $user = Auth::user();

                if (in_array('deleted_by', $model->getFillable()) || $model->isFillable('deleted_by')) {
                    $model->deleted_by = $user?->id;
                    $model->saveQuietly(); // hindari infinite loop
                }
            });

            static::restoring(function ($model) {
                // Clear deleted_by saat restore
                if (in_array('deleted_by', $model->getFillable()) || $model->isFillable('deleted_by')) {
                    $model->deleted_by = null;
                }

                // Update updated_by saat restore
                $user = Auth::user();
                $model->updated_by = $user?->id;
            });
        }
    }

    /**
     * Check if model uses SoftDeletes trait
     */
    private static function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::class));
    }
}
