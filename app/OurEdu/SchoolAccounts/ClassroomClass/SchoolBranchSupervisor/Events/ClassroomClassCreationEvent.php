<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClassroomClassCreationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $status;
    /**
     * @var null
     */
    private $errorException;
    /**
     * @var string
     */
    private $channelId;

    /**
     * Create a new event instance.
     *
     * @param string $channelId
     * @param $status
     * @param null $errorException
     */
    public function __construct(string $channelId, $status, $errorException = null)
    {
        $this->status = $status;
        $this->errorException = $errorException ?? [];
        $this->channelId = $channelId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('createClassroomClass.' . $this->channelId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            "status" => $this->status,
            "errors" => !$this->status ? $this->errorException : []
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'ClassroomClassCreationEvent';
    }
}
