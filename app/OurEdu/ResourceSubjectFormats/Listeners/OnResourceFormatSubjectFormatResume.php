<?php

namespace App\OurEdu\ResourceSubjectFormats\Listeners;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatResumedEvent;

class OnResourceFormatSubjectFormatResume
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionUpdated  $event
     * @return void
     */
    public function handle(ResourceFormatSubjectFormatResumedEvent $event)
    {
        Task::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('resource_subject_format_subject_id', $event->resourceFormatSubjectFormat->id)->update([
                'is_paused' =>  false
            ]);

        Task::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('resource_subject_format_subject_id', $event->resourceFormatSubjectFormat->id)
            ->whereNotNull('pulled_at')
            ->update([
                'pulled_at' =>  now()
            ]);


        QuestionReportTask::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('resource_subject_format_subject_id', $event->resourceFormatSubjectFormat->id)->update([
                'is_paused' =>  false
            ]);

        QuestionReportTask::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('resource_subject_format_subject_id', $event->resourceFormatSubjectFormat->id)
            ->whereNotNull('pulled_at')
            ->update([
                'pulled_at' =>  now()
            ]);
    }
}
