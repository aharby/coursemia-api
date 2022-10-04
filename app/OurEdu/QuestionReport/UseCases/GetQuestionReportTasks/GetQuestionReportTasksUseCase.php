<?php


namespace App\OurEdu\QuestionReport\UseCases\GetQuestionReportTasks;


use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\UserEnums;

class GetQuestionReportTasksUseCase implements GetQuestionReportTasksUseCaseInterface
{
    private $subjectRepository;
    private $questionReportTaskRepository;


    public function __construct(SubjectRepositoryInterface $subjectRepository,
                                QuestionReportTaskRepositoryInterface $questionReportTaskRepository)
    {
        $this->subjectRepository = $subjectRepository;
        $this->questionReportTaskRepository = $questionReportTaskRepository;
    }

    public function getSubjectTasks($subjectId, $user , array $filters = [])
    {
        $subject = $this->subjectRepository->findOrFail($subjectId);
        $subjectRepo = new SubjectRepository($subject);
        if ($user->type == UserEnums::SME_TYPE) {
            return $this->getSubjectTasksForSME($subjectRepo , $filters);
        }

        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            return $this->getSubjectTasksForContentAuthor($subjectRepo,$user , $filters);
        }
        return [];
    }

    public function getAllTasks($user , array $filters = [])
    {
        if ($user->type == UserEnums::SME_TYPE) {
            return $this->getAllTasksForSME($user , $filters);
        }

        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            return $this->getAllTasksForContentAuthor($user , $filters);
        }
        return [];
    }

    private function getSubjectTasksForSME($subjectRepo , array $filters = [])
    {
        return $subjectRepo->getSubjectQuestionReportTasksForSMEPaginated($filters);
    }

    private function getSubjectTasksForContentAuthor($subjectRepo,$user , array $filters = [])
    {
        return $subjectRepo->getSubjectActiveQuestionReportTasksForContentAuthorPaginated($user , $filters);
    }

    private function getAllTasksForSME($user , array $filters = [])
    {
        return $this->questionReportTaskRepository->getAllSMETasksPaginated($user , $filters);
    }

    private function getAllTasksForContentAuthor($user , array $filters = [])
    {
        return $this->questionReportTaskRepository->getAllContentAuthorActiveTasksPaginated($user , $filters);
    }
}
