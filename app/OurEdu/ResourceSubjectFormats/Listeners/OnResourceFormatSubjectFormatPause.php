<?php

namespace App\OurEdu\ResourceSubjectFormats\Listeners;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatPausedEvent;

class OnResourceFormatSubjectFormatPause
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionUpdated  $event
     * @return void
     */
    public function handle(ResourceFormatSubjectFormatPausedEvent $event)
    {
        Task::active()->where('resource_subject_format_subject_id', $event->resourceFormatSubjectFormat->id)->update([
            'is_paused' =>  true
        ]);

        QuestionReportTask::active()->where('resource_subject_format_subject_id', $event->resourceFormatSubjectFormat->id)->update([
            'is_paused' =>  true
        ]);
    }
}
