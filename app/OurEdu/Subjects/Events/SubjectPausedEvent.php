<?php

namespace App\OurEdu\Subjects\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubjectPausedEvent
{
    use SerializesModels, Dispatchable;
    
    public $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }
}
