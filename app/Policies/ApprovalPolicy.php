<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApprovalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only staff/officials can access approval dashboard
        return $user->hasAnyRole([
            'Staf Akademik',
            'Kepala Subbagian Akademik',
            'Wakil Dekan Bidang Akademik',
            'Dekan Fakultas Vokasi',
            'Ketua Program Studi',
            'Dosen',
            'Administrator'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Approval $approval): bool
    {
        // Administrator can view all
        if ($user->hasRole('Administrator')) {
            return true;
        }

        // Staf Akademik can view all
        if ($user->hasRole('Staf Akademik')) {
            return true;
        }

        // Check if user is involved in this approval
        $userPosition = $user->currentOfficialPosition?->position;

        if (!$userPosition) {
            return false;
        }

        // Can view if user position matches required positions
        return in_array($userPosition->value, $approval->required_positions ?? []);
    }

    /**
     * Determine if user can approve.
     */
    public function approve(User $user, Approval $approval): bool
    {
        // Must be pending and active
        if ($approval->status !== 'pending' || !$approval->is_active) {
            return false;
        }

        // Get user position
        $userPosition = $user->currentOfficialPosition?->position;

        if (!$userPosition) {
            return false;
        }

        // Check if user position in required_positions
        return in_array($userPosition->value, $approval->required_positions ?? []);
    }

    /**
     * Determine if user can reject.
     */
    public function reject(User $user, Approval $approval): bool
    {
        // Same logic as approve
        return $this->approve($user, $approval);
    }

    /**
     * Determine if user can edit content.
     */
    public function editContent(User $user, Approval $approval): bool
    {
        // Must be able to approve first
        if (!$this->approve($user, $approval)) {
            return false;
        }

        // Check if this step allows content editing
        return $approval->can_edit_content ?? false;
    }
}
