<?php


namespace App\OurEdu\Subjects\UseCases\PullTaskUseCase;

use App\OurEdu\Users\User;
use App\Exceptions\ErrorResponseException;
use Illuminate\Database\Eloquent\Collection;
use App\OurEdu\Subjects\Events\SubjectModified;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;

class PullTaskUseCase implements PullTaskUseCaseInterface
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
     * @return array
     */
    public function pullTask(int $taskId, User $user)
    {
        $contentAuthorId = $user->contentAuthor->id;
        $task = $this->taskRepository->findOrFail($taskId);

        if ($task->is_paused) {
            throw new ErrorResponseException(trans('api.Task is paused at the moment'),422);
        }

        $taskRepository = new TaskRepository($task);

        $subjectFormatSubject = $taskRepository->getSubjectFormatSubjectByTask();

        if ($subjectFormatSubject) {
            $tasks = new Collection();

            $tasks = $this->updateSubjectStructuralUseCase->getSubjectFormatSubjectTasks($subjectFormatSubject, true, $tasks);
            $tasksIds = $tasks->pluck('id')->toArray();
            $tasksIdsContentAuthorArray = [];
            foreach ($tasksIds as $id) {
                $tasksIdsContentAuthorArray[] = [
                    'task_id' => $id,
                    'content_author_id' => $contentAuthorId,
                    'created_at' => now(),
                    'updated_at' => now(),

                ];
            }

            $this->taskRepository->assignTasksToContentAuthor($tasksIdsContentAuthorArray);
            $this->taskRepository->makeTasksAssignedIn($tasksIds);

            SubjectModified::dispatch([], $task->subject->toArray(), 'Content Author pulled task', $task->toArray());

            $tasks = $this->updateSubjectStructuralUseCase->getSubjectFormatSubjectTasks($subjectFormatSubject, false);
            return $tasks;
        }
        return [];
    }
}
