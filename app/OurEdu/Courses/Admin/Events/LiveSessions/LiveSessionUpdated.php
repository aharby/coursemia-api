<?php

namespace App\OurEdu\Courses\Admin\Events\LiveSessions;

use Illuminate\Queue\SerializesModels;

class LiveSessionUpdated
{
    public $liveSession;

    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $liveSession)
    {
        $this->liveSession = $liveSession;
    }
}
