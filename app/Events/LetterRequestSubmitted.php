<?php

namespace App\Events;

use App\Models\LetterRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LetterRequestSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LetterRequest $letterRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(LetterRequest $letterRequest)
    {
        $this->letterRequest = $letterRequest;
    }
}
