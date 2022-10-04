<?php

namespace App\Events;

use App\OurEdu\Users\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoCallEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $branch_id, $parent, $student, $video_call_request;

    public function __construct($parent, $student, $branch_id, $video_call_request)
    {
        $this->parent = $parent;
        $this->branch_id = $branch_id;
        $this->student = $student;
        $this->video_call_request = $video_call_request;
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
        return 'VideoCallEvent';
    }


    public function broadcastWith()
    {
        return [
            'parent' => $this->parent,
            'videoCallRequest'=>$this->video_call_request,
            'message' => trans('video.parent request call',['parent'=>$this->parent->first_name . ' ' . $this->parent->last_name,'student'=>$this->student->name])
        ];
    }
}
