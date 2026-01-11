<?php

namespace App\Listeners;

use App\Events\ApprovalProcessed;
use App\Models\User;
use App\Notifications\ApprovalProcessedNotification;
use App\Notifications\NewApprovalTaskNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendApprovalProcessedNotifications implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ApprovalProcessed $event): void
    {
        $approval = $event->approval;
        $letter = $approval->letterRequest;
        $action = $event->action;   // 'approved' or 'rejected'
        $note = $event->note;

        // Notify Student
        if ($letter->student) {
            Notification::send(
                $letter->student,
                new ApprovalProcessedNotification($approval, $action, $note)
            );
        }

        // Notify Approvers
        if ($action === 'approved') {
            $nextApproval = $letter->approvals()
                ->where('step', '>', $approval->step)
                ->where('is_active', true)
                ->where('status', 'pending')
                ->orderBy('step', 'asc')
                ->first();

            if ($nextApproval) {
                if ($nextApproval->assigned_approver_id) {
                    $approver = User::find($nextApproval->assigned_approver_id);
                    if ($approver) {
                        // $approver->notify(new NewApprovalTaskNotification($nextApproval));
                        Notification::send($approver, new NewApprovalTaskNotification($nextApproval));
                    }
                } elseif (!empty($nextApproval->required_positions)) {
                    $approvers = User::whereHas('currentOfficialPosition', function ($query) use ($nextApproval) {
                        $query->whereIn('position', $nextApproval->required_positions);
                    })->get();

                    if ($approvers->isNotEmpty()) {
                        Notification::send($approvers, new NewApprovalTaskNotification($nextApproval));
                    }
                }
            }
        }
    }
}
