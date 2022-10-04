<?php


namespace App\OurEdu\QuestionReport\UseCases\GetQuestionReportTasks;


interface GetQuestionReportTasksUseCaseInterface
{
    public function getSubjectTasks($subjectId, $user , array $filters = []);

    public function getAllTasks($user , array $filters = []);
}
