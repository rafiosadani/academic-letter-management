<?php

namespace App\Events;

use App\Models\LetterRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LetterFinalized
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LetterRequest $letterRequest;
    public string $letterNumber;
    public string $downloadUrl;

    /**
     * Create a new event instance.
     */
    public function __construct(LetterRequest $letterRequest, string $letterNumber, string $downloadUrl)
    {
        $this->letterRequest = $letterRequest;
        $this->letterNumber = $letterNumber;
        $this->downloadUrl = $downloadUrl;
    }
}
