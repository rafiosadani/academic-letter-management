<?php

namespace App\Listeners;

use App\Events\ApprovalContentEdited;
use App\Notifications\ContentEditedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendContentEditedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ApprovalContentEdited $event): void
    {
        $approval = $event->approval;
        $letter = $approval->letterRequest;

        // Notify Student
        if ($letter->student) {
            Notification::send(
                $letter->student,
                new ContentEditedNotification($approval)
            );
        }
    }
}
