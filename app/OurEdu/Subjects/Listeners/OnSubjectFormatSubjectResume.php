<?php

namespace App\OurEdu\Subjects\Listeners;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\Subjects\Events\SubjectFormatSubjectResumedEvent;

class OnSubjectFormatSubjectResume
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionUpdated  $event
     * @return void
     */
    public function handle(SubjectFormatSubjectResumedEvent $event)
    {
        Task::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_format_subject_id', $event->subjectFormatSubject->id)->update([
                'is_paused' =>  false
            ]);

        Task::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_format_subject_id', $event->subjectFormatSubject->id)
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
            ->where('subject_format_subject_id', $event->subjectFormatSubject->id)->update([
                'is_paused' =>  false
            ]);

        QuestionReportTask::active()->whereHas('subject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_format_subject_id', $event->subjectFormatSubject->id)
            ->whereNotNull('pulled_at')
            ->update([
                'pulled_at' =>  now()
            ]);
    }
}
