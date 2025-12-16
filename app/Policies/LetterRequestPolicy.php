<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\LetterRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LetterRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::LETTER_MY_VIEW->value)
            || $user->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LetterRequest $letterRequest): bool
    {
        // Owner can view
        if ($letterRequest->student_id === $user->id) {
            return true;
        }

        // Staff / Admin
        if ($user->hasPermissionTo(PermissionName::LETTER_MY_VIEW->value)) {
            return true;
        }

        // Approver can view if assigned to approval step
        if ($letterRequest->approvals()->where('assigned_approver_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::LETTER_MY_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LetterRequest $letterRequest): bool
    {
        // Only owner can update
        if ($letterRequest->student_id !== $user->id) {
            return false;
        }

        return $letterRequest->canBeEditedByStudent();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LetterRequest $letterRequest): bool
    {
        // Owner can delete if still draft/rejected
        if ($letterRequest->student_id === $user->id) {
            return in_array($letterRequest->status, ['in_progress', 'rejected']);
        }

        // Staff/Admin can delete
        return $user->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value);
    }

    /**
     * Determine if user can cancel the letter request.
     */
    public function cancel(User $user, LetterRequest $letterRequest): bool
    {
        // Owner can cancel if in progress
        if ($letterRequest->student_id === $user->id) {
            return in_array($letterRequest->status, ['in_progress', 'resubmitted']);
        }

        return false;
    }
}
