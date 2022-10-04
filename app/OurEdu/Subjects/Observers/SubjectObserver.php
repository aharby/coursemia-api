<?php

namespace App\OurEdu\Subjects\Observers;

use App\OurEdu\BaseApp\Helpers\MailClass;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Users\UserEnums;

class SubjectObserver
{
    /**
     * Handle the subject "created" event.
     *
     * @param \App\Subject $subject
     * @return void
     */
    public function created(Subject $subject)
    {
    }

    /**
     * Handle the subject "updated" event.
     *
     * @param \App\Subject $subject
     * @return void
     */
    public function updated(Subject $subject)
    {
        //
    }

    /**
     * Handle the subject "deleted" event.
     *
     * @param \App\Subject $subject
     * @return void
     */
    public function deleted(Subject $subject)
    {
        if ($subject->childSubjectFormatSubject) {
            foreach ($subject->childSubjectFormatSubject as $subjectFormatSubject) {
                $subjectFormatSubject->delete();
            }
        }

        if ($subject->resourceSubjectFormatSubject) {
            foreach ($subject->resourceSubjectFormatSubject as $value) {
                $value->delete();
            }
        }
    }

    /**
     * Handle the subject "restored" event.
     *
     * @param \App\Subject $subject
     * @return void
     */
    public function restored(Subject $subject)
    {
        //
    }

    /**
     * Handle the subject "force deleted" event.
     *
     * @param \App\Subject $subject
     * @return void
     */
    public function forceDeleted(Subject $subject)
    {
        //
    }
}
