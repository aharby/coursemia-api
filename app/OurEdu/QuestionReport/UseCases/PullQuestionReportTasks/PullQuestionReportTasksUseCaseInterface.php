<?php


namespace App\OurEdu\QuestionReport\UseCases\PullQuestionReportTasks;


use App\OurEdu\Users\User;

interface PullQuestionReportTasksUseCaseInterface
{
    function pullTask(int $taskId,User $user);
}
