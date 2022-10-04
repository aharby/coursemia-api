<?php


namespace App\OurEdu\Subjects\UseCases\GetTasks;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;
use App\OurEdu\Subjects\UseCases\GetTasks\GetTasksUseCaseInterface;
use App\OurEdu\Users\UserEnums;

class GetTasksUseCase implements GetTasksUseCaseInterface
{
    private $subjectRepository;
    private $taskRepository;


    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        TaskRepositoryInterface $taskRepository
    ) {
        $this->subjectRepository = $subjectRepository;
        $this->taskRepository = $taskRepository;
    }

    public function getSubjectTasks($subjectId, $user,  $filters = [])
    {

        $subject = $this->subjectRepository->findOrFail($subjectId);
        $subjectRepo = new SubjectRepository($subject);
        if ($user->type == UserEnums::SME_TYPE) {
            return $this->taskRepository->getSubjectTasksPaginated($subject , $filters );
        }

        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            return $this->getSubjectTasksForContentAuthor($subjectRepo, $user,  $filters );
        }
        return [];
    }

    public function getAllTasks($user, $filters = [])
    {
        if ($user->type == UserEnums::SME_TYPE) {
            return $this->getAllTasksForSME($user, $filters);
        }

        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            return $this->getAllTasksForContentAuthor($user, $filters);
        }
        return [];
    }

    private function getSubjectTasksForContentAuthor($subjectRepo, $user,  $filters = [])
    {
        return $subjectRepo->getSubjectActiveTasksForContentAuthorPaginated($user, $filters);
    }

    private function getAllTasksForSME($user,  $filters = [])
    {
        return $this->taskRepository->getAllSMETasksPaginated($user,  $filters);
    }

    private function getAllTasksForContentAuthor($user, $filters = [])
    {
        return $this->taskRepository->getAllContentAuthorActiveTasksPaginated($user, $filters);
    }
}
