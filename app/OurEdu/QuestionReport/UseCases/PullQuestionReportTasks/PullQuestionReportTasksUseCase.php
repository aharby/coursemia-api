<?php


namespace App\OurEdu\QuestionReport\UseCases\PullQuestionReportTasks;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepository;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Collection;


class PullQuestionReportTasksUseCase implements PullQuestionReportTasksUseCaseInterface
{

    private $updateSubjectStructuralUseCase;
    private $questionReportTaskRepository;


    public function __construct(
        QuestionReportTaskRepositoryInterface $questionReportTaskRepository
    )
    {
        $this->questionReportTaskRepository = $questionReportTaskRepository;
    }

    /**
     * @param int $taskId
     * @param User $user
     * @return array
     */


    public function pullTask(int $taskId, User $user)
    {
        $contentAuthorId = $user->contentAuthor->id;
        $task = $this->questionReportTaskRepository->findOrFail($taskId);

        $tasksIdsContentAuthorArray[] = [
            'task_id' => $taskId,
            'content_author_id' => $contentAuthorId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $tasksIds = [$taskId];
        $this->questionReportTaskRepository->assignTasksToContentAuthor($tasksIdsContentAuthorArray);
        $this->questionReportTaskRepository->makeTasksAssignedIn($tasksIds);

        return $task;
    }
}
