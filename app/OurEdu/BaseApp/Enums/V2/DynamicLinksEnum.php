<?php


namespace App\OurEdu\BaseApp\Enums\V2;


final class DynamicLinksEnum
{
    const STUDENT_HOMEWORK = '{portal_url}/student/general-quizzes/homework/{homework_id}';
    const STUDENT_PERIODIC_TEST = '{portal_url}/student/general-quizzes/periodic-tests/{homework_id}';
    const STUDENT_COURSE_HOMEWORK = '{portal_url}/courses/{course_id}/answer-homework/{homework_id}';

}
