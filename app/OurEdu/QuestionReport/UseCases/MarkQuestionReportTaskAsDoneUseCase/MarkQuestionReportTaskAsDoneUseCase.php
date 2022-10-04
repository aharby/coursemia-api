<?php

namespace App\OurEdu\QuestionReport\UseCases\MarkQuestionReportTaskAsDoneUseCase;

use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;

class MarkQuestionReportTaskAsDoneUseCase implements MarkQuestionReportTaskAsDoneUseCaseInterface
{
    protected $taskRepository;
    protected $user;

    public function __construct(QuestionReportTaskRepositoryInterface $taskRepository)
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

        $this->taskRepository->markTaskAsDone($task);

        return $task;
    }
}
