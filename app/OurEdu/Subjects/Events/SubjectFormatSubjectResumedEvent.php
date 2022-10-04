<?php

namespace App\OurEdu\Subjects\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubjectFormatSubjectResumedEvent
{
    use SerializesModels, Dispatchable;
    
    public $subjectFormatSubject;

    public function __construct($subjectFormatSubject)
    {
        $this->subjectFormatSubject = $subjectFormatSubject;
    }
}
