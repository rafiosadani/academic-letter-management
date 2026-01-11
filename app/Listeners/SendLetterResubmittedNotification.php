<?php

namespace App\Listeners;

use App\Events\LetterResubmitted;
use App\Models\User;
use App\Notifications\LetterResubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendLetterResubmittedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LetterResubmitted $event): void
    {
        $letter = $event->letterRequest;
        $previousRejectionReason = $event->previousRejectionReason;

        $firstApproval = $letter->approvals()
            ->where('step', 1)
            ->where('is_active', true)
            ->first();

        if (!$firstApproval) return;

        // Notify Approver(s)
        if ($firstApproval->assigned_approver_id) {
            $approver = User::find($firstApproval->assigned_approver_id);
            if ($approver) {
                Notification::send($approver, new LetterResubmittedNotification($letter, $previousRejectionReason));
            }
        } elseif (!empty($firstApproval->required_positions)) {
            $approvers = User::whereHas('currentOfficialPosition', function ($query) use ($firstApproval) {
                $query->whereIn('position', $firstApproval->required_positions);
            })->get();

            if ($approvers->isNotEmpty()) {
                Notification::send($approvers, new LetterResubmittedNotification($letter, $previousRejectionReason));
            }
        }
    }
}
