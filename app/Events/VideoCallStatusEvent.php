<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoCallStatusEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $supervisor, $status, $branch_id, $token, $video_call_request;
    private $channel;

    public function __construct($supervisor, $status, $branch_id, $token, $video_call_request, $channel)
    {
        $this->supervisor = $supervisor;
        $this->status = $status;
        $this->branch_id = $branch_id;
        $this->token = $token;
        $this->video_call_request = $video_call_request;
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('Branch.' . $this->branch_id);
    }

    public function broadcastAs()
    {
        return 'VideoCallStatusEvent';
    }


    public function broadcastWith()
    {
        return [
            'status' => $this->status,
            'video_call_request' => $this->video_call_request,
            'message' => $this->status == 'accept' ? 'Invitation Accepted' : 'Invitation Rejected',
            'token' => $this->token,
            'channel' => $this->channel
        ];
    }
}
