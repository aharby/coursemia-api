<?php

namespace App\OurEdu\Subjects\Listeners;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Events\SubjectResumedEvent;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;

class OnSubjectResume
{
    /**
     * Handle the event.
     *
     * @param  LiveSessionUpdated  $event
     * @return void
     */
    public function handle(SubjectResumedEvent $event)
    {
        Task::active()->whereHas('resourceSubjectFormatSubject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_id', $event->subject->id)->update([
                'is_paused' =>  false
            ]);

        Task::active()->whereHas('resourceSubjectFormatSubject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_id', $event->subject->id)
            ->whereNotNull('pulled_at')
            ->update([
                'pulled_at' =>  now()
            ]);

        QuestionReportTask::active()->whereHas('resourceSubjectFormatSubject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_id', $event->subject->id)->update([
                'is_paused' =>  false
            ]);

        QuestionReportTask::active()->whereHas('resourceSubjectFormatSubject', function ($q) {
            $q->where('is_active', true);
        })
            ->whereHas('subjectFormatSubject', function ($q) {
                $q->where('is_active', true);
            })
            ->where('subject_id', $event->subject->id)
            ->whereNotNull('pulled_at')
            ->update([
                'pulled_at' =>  now()
            ]);
    }
}
