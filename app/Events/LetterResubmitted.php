<?php

namespace App\Events;

use App\Models\LetterRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LetterResubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LetterRequest $letterRequest;
    public ?string $previousRejectionReason;

    /**
     * Create a new event instance.
     */
    public function __construct(LetterRequest $letterRequest, ?string $previousRejectionReason = null)
    {
        $this->letterRequest = $letterRequest;
        $this->previousRejectionReason = $previousRejectionReason;
    }
}
