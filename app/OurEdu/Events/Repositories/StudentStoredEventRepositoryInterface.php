<?php

namespace App\OurEdu\Events\Repositories;

interface StudentStoredEventRepositoryInterface
{
    /**
     * @param $userId
     * */
    public function getStudentEvents($userId);

    /**
     * @param $subjectId
     * */
    public function getStudentEventsBySubjectId($subjectId);

    /**
     * @param $userId
     * @param $subjectId
     * */
    public function getStudentEventsBySubjectAndStudent($userId, $subjectId);
}
