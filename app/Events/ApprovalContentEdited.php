<?php

namespace App\Events;

use App\Models\Approval;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalContentEdited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Approval $approval;
    public array $oldData;
    public array $newData;

    /**
     * Create a new event instance.
     */
    public function __construct(Approval $approval, array $oldData, array $newData)
    {
        $this->approval = $approval;
        $this->oldData = $oldData;
        $this->newData = $newData;
    }
}
