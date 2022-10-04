<?php

namespace App\OurEdu\VCRSchedules\Instructor\Events;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InstructorRejectRequestedSession implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public VCRSession $vcrSession)
    {
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('instructor-join-session.' . $this->vcrSession->id);
    }

    public function broadcastAs()
    {
        return 'InstructorRejectRequestedSession';
    }

    public function broadcastWith()
    {
        return
            [
                'status' => 422,
                'detail' => trans('vcr.error getting vcr session, vcr session waiting time has passed'),
                'title' => 'error getting vcr session vcr session waiting time has passed'
            ];
    }
}
