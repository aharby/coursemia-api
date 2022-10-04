<?php

namespace App\OurEdu\Courses\Admin\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CourseSessionCanceled
{
    use SerializesModels;
    
    public $courseSession;

    public function __construct($courseSession)
    {
        $this->courseSession = $courseSession;
    }
}
