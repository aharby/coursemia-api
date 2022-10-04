<?php

namespace App\Events;

use App\OurEdu\Users\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoCallCancelEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $parent, $branch_id;

    public function __construct(User $parent, $branch_id)
    {
        $this->parent = $parent;
        $this->branch_id = $branch_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('Branch.' . $this->branch_id);
    }

    public function broadcastAs()
    {
        return 'VideoCallCancelEvent';
    }


    public function broadcastWith()
    {
        return [
            'parent' => $this->parent,
            'message' => $this->parent->first_name . ' ' . $this->parent->last_name .
                ' cancelled video call for student '
        ];
    }
}
