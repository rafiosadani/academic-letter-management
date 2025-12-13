<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\ApprovalFlow;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApprovalFlowPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::APPROVAL_FLOW_VIEW->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApprovalFlow $approvalFlow): bool
    {
        return $user->hasPermissionTo(PermissionName::APPROVAL_FLOW_VIEW->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::APPROVAL_FLOW_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApprovalFlow $approvalFlow): bool
    {
        return $user->hasPermissionTo(PermissionName::APPROVAL_FLOW_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApprovalFlow $approvalFlow): bool
    {
        return $user->hasPermissionTo(PermissionName::APPROVAL_FLOW_DELETE->value);
    }
}
