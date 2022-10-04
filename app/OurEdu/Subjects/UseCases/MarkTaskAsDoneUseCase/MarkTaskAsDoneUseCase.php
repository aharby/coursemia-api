<?php

namespace App\OurEdu\Subjects\UseCases\MarkTaskAsDoneUseCase;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;

class MarkTaskAsDoneUseCase implements MarkTaskAsDoneUseCaseInterface
{
    protected $taskRepository;
    protected $user;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function markTaskAsDone(int $taskId)
    {
        $user = Auth::guard('api')->user();
        $contentAuthorId = $user->contentAuthor->id;
        $task = $this->taskRepository->findOrFail($taskId);

        // already done
        if ($task->is_done) {
            throw new ErrorResponseException(trans('api.Task already done'));
        }

        if ($task->is_paused) {
            throw new ErrorResponseException(trans('api.Task is paused at the moment'));
        }

        $taskRepository = new TaskRepository($task);

        $taskRepository->update([
            'is_done'   =>  1,
            'is_active' =>  0
        ]);

        return $task;
    }
}
