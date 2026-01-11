<?php

namespace App\Listeners;

use App\Events\LetterRequestSubmitted;
use App\Models\User;
use App\Notifications\LetterSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendLetterSubmissionNotifications implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LetterRequestSubmitted $event): void
    {
//        $letter = $event->letterRequest;
//
//        // 1. Notify Student (confirmation)
//        Notification::send(
//            $letter->student->user,
//            new LetterSubmittedNotification($letter, 'student')
//        );
//
//        // 2. Notify First Approver (new task)
//        $firstApproval = $letter->approvals()
//            ->where('step', 1)
//            ->where('is_active', true)
//            ->first();
//
//        if ($firstApproval && $firstApproval->assignedApprover) {
//            Notification::send(
//                $firstApproval->assignedApprover->user,
//                new LetterSubmittedNotification($letter, 'approver')
//            );
//        }


        $letter = $event->letterRequest;

        // Notify Student
        if ($letter->student) {
            Notification::send(
                $letter->student,
                new LetterSubmittedNotification($letter, 'student')
            );
        }

        $firstApproval = $letter->approvals()
            ->where('step', 1)
            ->where('is_active', true)
            ->first();

        if (!$firstApproval) return;

        // Notify Approver(s)
        if ($firstApproval->assigned_approver_id) {
            $approver = User::find($firstApproval->assigned_approver_id);
            if ($approver) {
                Notification::send($approver, new LetterSubmittedNotification($letter, 'approver'));
            }
        } elseif (!empty($firstApproval->required_positions)) {
            $approvers = User::whereHas('currentOfficialPosition', function ($query) use ($firstApproval) {
                $query->whereIn('position', $firstApproval->required_positions);
            })->get();

            if ($approvers->isNotEmpty()) {
                Notification::send($approvers, new LetterSubmittedNotification($letter, 'approver'));
            }
        }
    }
}
