<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\FacultyOfficial;
use App\Models\User;

class FacultyOfficialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_VIEW->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FacultyOfficial $facultyOfficial): bool
    {
        return $user->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_VIEW->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FacultyOfficial $facultyOfficial): bool
    {
        return $user->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FacultyOfficial $facultyOfficial): bool
    {
        // Cannot delete own active assignment
        if ($facultyOfficial->user_id === $user->id && $facultyOfficial->is_active) {
            return false;
        }

        return $user->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_DELETE->value);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FacultyOfficial $facultyOfficial): bool
    {
        return $user->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_DELETE->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FacultyOfficial $facultyOfficial): bool
    {
        // Only Administrator can force delete
        return $user->hasRole('Administrator');
    }
}
