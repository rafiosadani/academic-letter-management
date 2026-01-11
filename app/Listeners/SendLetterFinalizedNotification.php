<?php

namespace App\Listeners;

use App\Events\LetterFinalized;
use App\Notifications\LetterFinalizeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendLetterFinalizedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LetterFinalized $event): void
    {
        $letter = $event->letterRequest;
        $letterNumber = $event->letterNumber;
        $downloadUrl = $event->downloadUrl;

        // Notify Student
        if ($letter->student) {
            Notification::send(
                $letter->student,
                new LetterFinalizeNotification($letter, $letterNumber, $downloadUrl)
            );
        }
    }
}
