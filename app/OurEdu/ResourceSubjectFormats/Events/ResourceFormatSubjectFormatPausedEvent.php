<?php

namespace App\OurEdu\ResourceSubjectFormats\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ResourceFormatSubjectFormatPausedEvent
{
    use SerializesModels, Dispatchable;
    
    public $resourceFormatSubjectFormat;

    public function __construct($resourceFormatSubjectFormat)
    {
        $this->resourceFormatSubjectFormat = $resourceFormatSubjectFormat;
    }
}
