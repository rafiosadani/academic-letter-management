<?php

namespace App\Events;

use App\Models\Approval;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Approval $approval;
    public string $action;
    public ?string $note;

    /**
     * Create a new event instance.
     */
    public function __construct(Approval $approval, string $action, ?string $note = null)
    {
        $this->approval = $approval;
        $this->action = $action;
        $this->note = $note;
    }
}
