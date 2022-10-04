<?php

namespace App\OurEdu\Events\Enums;

class StudentEventsEnum
{
    const STUDENT_STARTED_EXAM = 'student_started_exam';
    const STUDENT_FINISHED_EXAM = 'student_finished_exam';
    const STUDENT_STARTED_PRACTICE = 'student_started_practice';
    const STUDENT_FINISHED_PRACTICE = 'student_finished_practice';
    const STUDENT_STARTED_COMPETITION = 'student_started_competition';
    const STUDENT_JOINED_COMPETITION = 'student_joined_competition';
    const STUDENT_FINISHED_COMPETITION = 'student_finished_competition';
}
