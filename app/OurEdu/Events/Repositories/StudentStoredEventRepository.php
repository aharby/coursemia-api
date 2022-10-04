<?php

namespace App\OurEdu\Events\Repositories;

use App\OurEdu\Events\Models\StudentStoredEvent;


class StudentStoredEventRepository implements StudentStoredEventRepositoryInterface
{
    public $studentEvent;

    public function __construct(StudentStoredEvent $studentEvent)
    {
        $this->studentEvent = $studentEvent;
    }

    /**
     * getting the student's related events
     * the used ID is the USER object Id NOT the STUDENT object Id
     * @param $userId
     * @return
     */
    public function getStudentEvents($userId)
    {
        return $this->studentEvent
            ->where("event_properties->user_attributes->id", $userId)
            ->latest()
            ->jsonPaginate();
    }

    /**
     * getting the student's related events
     * the used ID is the USER object Id NOT the STUDENT object Id
     * @param $subjectId
     * @return
     */
    public function getStudentEventsBySubjectId($subjectId)
    {
        return $this->studentEvent
            ->where("event_properties->subject_attributes->subject_id", $subjectId)
            ->latest()
            ->jsonPaginate();
    }

    public function getStudentEventsBySubjectAndStudent($userId, $subjectId)
    {
        return $this->studentEvent
            ->where("event_properties->user_attributes->id", $userId)
            ->where("event_properties->subject_attributes->subject_id", $subjectId)
            ->latest()
            ->jsonPaginate();
    }
}
