<?php

namespace App\OurEdu\Subjects\Listeners;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Events\SubjectPausedEvent;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;

class OnSubjectPause
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionUpdated  $event
     * @return void
     */
    public function handle(SubjectPausedEvent $event)
    {
        Task::active()->where('subject_id', $event->subject->id)->update([
            'is_paused' =>  true
        ]);

        QuestionReportTask::active()->where('subject_id', $event->subject->id)->update([
            'is_paused' =>  true
        ]);
    }
}
