<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\LetterNumberConfig;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LetterNumberConfigPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::SETTINGS_LETTER_NUMBER_VIEW->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LetterNumberConfig $letterNumberConfig): bool
    {
        return $user->hasPermissionTo(PermissionName::SETTINGS_LETTER_NUMBER_VIEW->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::SETTINGS_LETTER_NUMBER_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LetterNumberConfig $letterNumberConfig): bool
    {
        return $user->hasPermissionTo(PermissionName::SETTINGS_LETTER_NUMBER_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LetterNumberConfig $letterNumberConfig): bool
    {
        return $user->hasPermissionTo(PermissionName::SETTINGS_LETTER_NUMBER_DELETE->value);
    }
}
