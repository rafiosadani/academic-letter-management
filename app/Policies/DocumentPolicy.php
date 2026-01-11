<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function upload(User $user): bool
    {
        return $user->hasPermissionTo(PermissionName::LETTER_MY_CREATE->value)
            || $user->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value);
    }

    public function download(User $user, Document $document): bool
    {
        $letter = $document->letterRequest;

        if ($letter->student_id === $user->id) {
            return true;
        }

        if ($user->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        $letter = $document->letterRequest;

        if ($document->category !== 'supporting') {
            return false;
        }

        if ($letter->student_id === $user->id && $letter->status === 'draft') {
            return true;
        }

        if ($user->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value)) {
            return true;
        }

        return false;
    }

   public function uploadExternal(User $user): bool
   {
       return $user->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value);
   }
}
