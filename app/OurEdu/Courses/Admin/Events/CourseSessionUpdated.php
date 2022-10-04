<?php

namespace App\OurEdu\Courses\Admin\Events;

use Illuminate\Queue\SerializesModels;
use App\OurEdu\Courses\Models\SubModels\CourseSession;

class CourseSessionUpdated
{
    public $courseSession;

    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CourseSession $courseSession)
    {
        $this->courseSession = $courseSession->load('course');
    }
}
