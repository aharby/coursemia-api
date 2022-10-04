<?php
namespace App\OurEdu\Subjects\UseCases\GetTasks;

interface GetTasksUseCaseInterface
{
    public function getSubjectTasks($subjectId, $user, $filters = []);

    public function getAllTasks($user, $filters = []);
}
