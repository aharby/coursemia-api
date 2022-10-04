<?php

namespace App\OurEdu\Subjects\Listeners;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\Subjects\Events\SubjectFormatSubjectPausedEvent;

class OnSubjectFormatSubjectPause
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionUpdated  $event
     * @return void
     */
    public function handle(SubjectFormatSubjectPausedEvent $event)
    {
        Task::active()->where('subject_format_subject_id', $event->subjectFormatSubject->id)->update([
            'is_paused' =>  true
        ]);

        QuestionReportTask::active()->where('subject_format_subject_id', $event->subjectFormatSubject->id)->update([
            'is_paused' =>  true
        ]);
    }
}
