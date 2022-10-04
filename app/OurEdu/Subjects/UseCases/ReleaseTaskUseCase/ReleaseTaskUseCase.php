<?php


namespace App\OurEdu\Subjects\UseCases\ReleaseTaskUseCase;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\User;
use App\Exceptions\ErrorResponseException;
use Illuminate\Database\Eloquent\Collection;
use App\OurEdu\Subjects\Events\SubjectModified;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;

class ReleaseTaskUseCase implements ReleaseTaskUseCaseInterface
{
    private $updateSubjectStructuralUseCase;
    private $taskRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
        UpdateSubjectStructuralUseCaseInterface $updateSubjectStructuralUseCase
    ) {
        $this->updateSubjectStructuralUseCase = $updateSubjectStructuralUseCase;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param int $taskId
     * @param User $user
     * @return Task $task
     */
    public function releaseTask(int $taskId, User $user)
    {
        $contentAuthorId = $user->contentAuthor->id;
        $task = $this->taskRepository->findOrFail($taskId);

        if ($task->is_paused) {
            throw new ErrorResponseException(trans('api.Task is paused at the moment'),422);
        }

        if ($task->is_done) {
            throw new ErrorResponseException(trans('api.Task can not be released after being done'),422);
        }

        $this->taskRepository->unAssignTasksFromContentAuthor( $contentAuthorId , [$task->id] );
        $this->taskRepository->makeTasksUnAssigned([$task->id]);

        SubjectModified::dispatch([], $task->subject->toArray(), 'Content Author released task', $task->toArray());

        return $task;
    }
}
